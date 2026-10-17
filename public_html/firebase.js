import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

const firebaseConfig = {
  apiKey: "AIzaSyDXfCEP2-Jc0KlLcXWfmfPcCezpUjqqW8o",
  authDomain: "cerodistancia-48655.firebaseapp.com",
  projectId: "cerodistancia-48655",
  storageBucket: "cerodistancia-48655.firebasestorage.app",
  messagingSenderId: "72751836246",
  appId: "1:72751836246:web:8ee841e0a5bb5dce185ff7"
};

const app = initializeApp(firebaseConfig);
export const auth = getAuth(app);
