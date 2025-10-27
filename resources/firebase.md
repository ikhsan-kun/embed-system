// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyDhycfEGu87026MUPvDUSKw4phNSj71m3o",
  authDomain: "embed-system-73444.firebaseapp.com",
  databaseURL: "https://embed-system-73444-default-rtdb.firebaseio.com",
  projectId: "embed-system-73444",
  storageBucket: "embed-system-73444.firebasestorage.app",
  messagingSenderId: "771584552846",
  appId: "1:771584552846:web:5dee761c77e042c6913edf",
  measurementId: "G-VLM0H5WZMX"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);