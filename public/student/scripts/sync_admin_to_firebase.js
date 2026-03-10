const admin = require('firebase-admin');
const fs = require('fs');
const path = require('path');

// 1. Path to your Firebase Service Account Key
const serviceAccountPath = path.resolve(__dirname, '../../fee-management-16c9e-firebase-adminsdk-fbsvc-2065458d80.json');

if (!fs.existsSync(serviceAccountPath)) {
    console.error('Error: serviceAccountKey.json not found in the project root.');
    console.error('Please download your service account key from the Firebase Console and place it in the project root directory.');
    process.exit(1);
}

const serviceAccount = require(path.resolve(serviceAccountPath));

admin.initializeApp({
    credential: admin.credential.cert(serviceAccount)
});

const syncAdmin = async () => {
    const adminEmail = 'admin@school.edu';
    const adminPassword = 'admin123';
    const adminDisplayName = 'School Administrator';

    try {
        console.log(`Checking for admin user: ${adminEmail}...`);
        
        let user;
        try {
            user = await admin.auth().getUserByEmail(adminEmail);
            console.log('Admin user already exists in Firebase Auth.');
        } catch (error) {
            if (error.code === 'auth/user-not-found') {
                console.log('Admin user not found. Creating...');
                user = await admin.auth().createUser({
                    email: adminEmail,
                    password: adminPassword,
                    displayName: adminDisplayName,
                });
                console.log('Successfully created new admin user.');
            } else {
                throw error;
            }
        }

        await admin.auth().setCustomUserClaims(user.uid, { admin: true });
        console.log('Admin custom claims set successfully.');

        console.log('\n--- Sync Complete ---');
        console.log('Email:', adminEmail);
        console.log('Password:', adminPassword);
        process.exit(0);
    } catch (error) {
        console.error('Error syncing admin:', error);
        process.exit(1);
    }
};

syncAdmin();
