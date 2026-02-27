#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const admin = require('firebase-admin');

const SQL_PATH = path.join(__dirname, '..', 'school_students.sql');

const serviceAccountPath = process.env.GOOGLE_APPLICATION_CREDENTIALS || process.argv[2];
if (!serviceAccountPath) {
  console.error('\nERROR: Service account JSON path required.');
  console.error('Provide path as first argument or set GOOGLE_APPLICATION_CREDENTIALS env var.');
  console.error('\nExample:');
  console.error('  node scripts/import_sql_to_firestore.js ./serviceAccountKey.json');
  process.exit(1);
}

if (!fs.existsSync(SQL_PATH)) {
  console.error('Cannot find school_students.sql at', SQL_PATH);
  process.exit(1);
}

if (!fs.existsSync(serviceAccountPath)) {
  console.error('Service account file not found at', serviceAccountPath);
  process.exit(1);
}

admin.initializeApp({
  credential: admin.credential.cert(require(path.resolve(serviceAccountPath)))
});

const db = admin.firestore();
const sql = fs.readFileSync(SQL_PATH, 'utf8');
const now = new Date();

function extractInsertBlock(tableName) {
  const re = new RegExp("INSERT\\s+INTO\\s+" + tableName + "\\s*\\(([^)]+)\\)\\s*VALUES\\s*(.+?);","is");
  const m = sql.match(re);
  if (!m) return null;
  return { columns: m[1].trim(), valuesBlock: m[2].trim() };
}

function extractTuples(valuesBlock) {
  const tuples = [];
  let depth = 0;
  let start = -1;
  for (let i = 0; i < valuesBlock.length; i++) {
    const ch = valuesBlock[i];
    if (ch === '(') {
      if (depth === 0) start = i;
      depth++;
    } else if (ch === ')') {
      depth--;
      if (depth === 0 && start !== -1) {
        tuples.push(valuesBlock.substring(start + 1, i));
        start = -1;
      }
    }
  }
  return tuples;
}

function splitTopLevelCommas(s) {
  const parts = [];
  let cur = '';
  let inQuote = false;
  for (let i = 0; i < s.length; i++) {
    const ch = s[i];
    if (ch === "'") {
      inQuote = !inQuote;
      cur += ch;
    } else if (ch === ',' && !inQuote) {
      parts.push(cur.trim());
      cur = '';
    } else {
      cur += ch;
    }
  }
  if (cur.trim() !== '') parts.push(cur.trim());
  return parts;
}

function cleanValue(val) {
  if (!val) return null;
  val = val.trim();
  if (/^NULL$/i.test(val)) return null;
  if (/^\'.*\'$/.test(val)) {
    // single-quoted string
    return val.slice(1, -1).replace(/''/g, "'");
  }
  // Numbers
  if (!isNaN(val)) return Number(val);
  // NOW() or CURDATE()
  if (/NOW\(\)/i.test(val) || /CURDATE\(\)/i.test(val)) return admin.firestore.Timestamp.fromDate(now);
  // DATE_SUB(NOW(), INTERVAL N DAY)
  const ds = val.match(/DATE_SUB\([^,]+,\s*INTERVAL\s+(\d+)\s+DAY\)/i);
  if (ds) {
    const days = parseInt(ds[1], 10);
    const d = new Date(now.getTime() - days * 24 * 60 * 60 * 1000);
    return admin.firestore.Timestamp.fromDate(d);
  }
  // DATE_SUB(NOW(), INTERVAL N MINUTE/SECOND)
  const ds2 = val.match(/DATE_SUB\([^,]+,\s*INTERVAL\s+(\d+)\s+(MINUTE|SECOND)\)/i);
  if (ds2) {
    const n = parseInt(ds2[1], 10);
    const unit = ds2[2].toUpperCase();
    let d = new Date(now);
    if (unit === 'MINUTE') d = new Date(now.getTime() - n * 60 * 1000);
    if (unit === 'SECOND') d = new Date(now.getTime() - n * 1000);
    return admin.firestore.Timestamp.fromDate(d);
  }
  // DATE_ADD(CURDATE(), INTERVAL N DAY)
  const da = val.match(/DATE_ADD\([^,]+,\s*INTERVAL\s+(\d+)\s+DAY\)/i);
  if (da) {
    const days = parseInt(da[1], 10);
    const d = new Date(now.getTime() + days * 24 * 60 * 60 * 1000);
    return admin.firestore.Timestamp.fromDate(d);
  }
  // Fallback: return string as-is (remove surrounding quotes if present)
  return val.replace(/^\'+|\'+$/g, '');
}

async function importTable(tableName, collectionName, idField) {
  const block = extractInsertBlock(tableName);
  if (!block) {
    console.log(`No INSERT block found for ${tableName}`);
    return 0;
  }
  const cols = block.columns.split(',').map(c => c.trim());
  const tuples = extractTuples(block.valuesBlock);
  let count = 0;
  for (const t of tuples) {
    const values = splitTopLevelCommas(t).map(v => cleanValue(v));
    const doc = {};
    cols.forEach((col, idx) => {
      doc[col] = values[idx] !== undefined ? values[idx] : null;
    });
    // Normalize fields: MySQL columns like created_at -> Timestamp if null
    if (doc.created_at == null) doc.created_at = admin.firestore.Timestamp.fromDate(now);
    if (doc.payment_date === undefined) doc.payment_date = null;

    // Insert into Firestore
    try {
      if (idField && doc[idField]) {
        await db.collection(collectionName).doc(String(doc[idField])).set(doc);
      } else {
        await db.collection(collectionName).add(doc);
      }
      count++;
    } catch (err) {
      console.error('Write error for', collectionName, err);
    }
  }
  console.log(`Imported ${count} records into ${collectionName}`);
  return count;
}

(async () => {
  try {
    console.log('Starting import to Firestore...');
    // Students: use student_id as document id
    await importTable('students', 'students', 'student_id');
    // Fees: create documents in 'fees' collection
    await importTable('fees', 'fees', null);
    // Appointments
    await importTable('appointments', 'appointments', null);
    // Queue
    await importTable('queue', 'queue', null);
    // Payment deadlines - optional: attempt import
    await importTable('payment_deadlines', 'payment_deadlines', null);

    console.log('Import complete.');
    process.exit(0);
  } catch (err) {
    console.error('Fatal error during import:', err);
    process.exit(1);
  }
})();
