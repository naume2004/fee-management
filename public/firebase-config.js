// Firebase configuration
// NOTE: replace the placeholders below with the values from
// Firebase Console → Project settings → Your apps (Web app) config
const firebaseConfig = {
    apiKey: "AIzaSyC5INQb5co3EHBQmRbYlxX5tE5oxASiamE",
    authDomain: "fee-management-16c9e.firebaseapp.com",
    projectId: "fee-management-16c9e",
    storageBucket: "fee-management-16c9e.firebasestorage.app",
    messagingSenderId: "877031179979",
    appId: "1:877031179979:web:a08e0e16699299c8d5bd4e",
    measurementId: "G-LNXNCSGB3K"
};

// Initialize Firebase (v8 syntax used by the site)
if (typeof firebase !== 'undefined') {
    firebase.initializeApp(firebaseConfig);
    var auth = firebase.auth();
    var db = firebase.firestore();
} else {
    console.warn('Firebase SDK not loaded - ensure <script> includes firebase-app.js and firebase-firestore.js');
}
