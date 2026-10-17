import { auth } from "./firebase.js";
import {
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword,
  GoogleAuthProvider,
  signInWithPopup,
  signInWithRedirect,
  getRedirectResult
} from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

function setSessionAndGo(user) {
  localStorage.setItem("uid", user.uid);
  localStorage.setItem("email", user.email || "");
  window.location.href = "panel.html";
}


function showMsg(text) {
  const el = document.getElementById("msg");
  if (el) el.innerText = text || "";
}

function isMobileDevice() {
  // Detecta iOS/Android + navegadores móviles
  return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
}

document.addEventListener("DOMContentLoaded", async () => {
  showMsg("");

  // -------------------------------
  // 1) RESULTADO DE REDIRECT (MÓVIL)
  // -------------------------------
  try {
    const redirectResult = await getRedirectResult(auth);
    if (redirectResult && redirectResult.user) {
      setSessionAndGo(redirectResult.user);
      return;
    }
  } catch (error) {
    // Si vuelve con error, lo mostramos
    console.error("getRedirectResult:", error.code, error.message);
    showMsg(error.message);
  }

  // -------------------------------
  // 2) LOGIN EMAIL/PASSWORD
  // -------------------------------
  const btnLogin = document.getElementById("btnLogin");
  if (btnLogin) {
    btnLogin.addEventListener("click", async () => {
      showMsg("");
      const email = document.getElementById("email").value.trim();
      const pass  = document.getElementById("password").value.trim();

      try {
        const userCredential = await signInWithEmailAndPassword(auth, email, pass);
        setSessionAndGo(userCredential.user);
      } catch (error) {
        console.error("login email:", error.code, error.message);
        showMsg(error.message);
      }
    });
  }

  // -------------------------------
  // 3) REGISTRO EMAIL/PASSWORD
  // -------------------------------
  const btnReg = document.getElementById("btnConfirmRegister");
  if (btnReg) {
    btnReg.addEventListener("click", async () => {
      const msg = document.getElementById("regMsg");
      if (msg) msg.innerText = "";

      const email = document.getElementById("regEmail").value.trim();
      const pass  = document.getElementById("regPassword").value.trim();

      try {
        const userCredential = await createUserWithEmailAndPassword(auth, email, pass);
        setSessionAndGo(userCredential.user);
      } catch (error) {
        console.error("register:", error.code, error.message);
        if (msg) msg.innerText = error.message;
      }
    });
  }

  // -------------------------------
  // 4) LOGIN GOOGLE (DESKTOP POPUP / MÓVIL REDIRECT)
  // -------------------------------
  const btnGoogle = document.getElementById("btnGoogle");
  if (btnGoogle) {
    btnGoogle.addEventListener("click", async () => {
      showMsg("");
      const provider = new GoogleAuthProvider();

      // Recomendado para que te pida cuenta siempre (opcional)
      provider.setCustomParameters({ prompt: "select_account" });

      try {
       btnGoogle.addEventListener("click", async () => {
  showMsg("");
  const provider = new GoogleAuthProvider();
  provider.setCustomParameters({ prompt: "select_account" });
  await signInWithRedirect(auth, provider);
});


        // DESKTOP: popup
        const result = await signInWithPopup(auth, provider);
        setSessionAndGo(result.user);

      } catch (error) {
        console.error("google:", error.code, error.message);

        // Si popup fue bloqueado o no permitido, caemos a redirect
        if (
          error.code === "auth/popup-blocked" ||
          error.code === "auth/popup-closed-by-user" ||
          error.code === "auth/operation-not-supported-in-this-environment"
        ) {
          try {
            await signInWithRedirect(auth, provider);
            return;
          } catch (e2) {
            console.error("google redirect fallback:", e2.code, e2.message);
            showMsg(e2.message);
            return;
          }
        }

        showMsg(error.message);
      }
    });
  }
});
