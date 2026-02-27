Firestore Seeding — quick guide

This repository includes a helper script to import the sample data in `school_students.sql` into Firestore.

Prerequisites
- Node.js (LTS) and npm installed
- A Firebase project and a service account JSON with Firestore permissions

Steps
1. Install dependencies

```powershell
cd C:\xampp\htdocs\fee-management
npm install
```

2. Download a Firebase service account JSON from the Firebase Console -> Project Settings -> Service accounts -> Generate new private key. Save the file into the project folder (for example `serviceAccountKey.json`).

3. Run the importer (either set env var or pass path as arg):

```powershell
# Option A: pass path as argument
node scripts/import_sql_to_firestore.js ./serviceAccountKey.json

# Option B: set env var
$env:GOOGLE_APPLICATION_CREDENTIALS = "C:\path\to\serviceAccountKey.json"
node scripts/import_sql_to_firestore.js
```

Notes
- The script attempts to parse the `INSERT INTO ... VALUES` blocks for tables: `students`, `fees`, `appointments`, `queue`, and `payment_deadlines`.
- Date expressions like `NOW()` and `DATE_SUB(NOW(), INTERVAL N DAY)` are approximated using the current system time.
- `fees` and other tables are written to Firestore collections named `fees`, `appointments`, etc. `students` documents use the `student_id` as the document ID.
- After import, verify in the Firebase Console > Firestore that collections and documents exist.

If you'd like, I can also:
- Add Cloud Function triggers to keep data consistent
- Create a one-shot admin script that runs in Cloud Shell
- Convert this to a safer transactional importer that maps SQL `fee_id` references to Firestore doc IDs

