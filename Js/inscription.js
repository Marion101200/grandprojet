
    const mdp = document.getElementById("password")
    const confirmPassword = document.getElementById("confirm-password")
    const forceMdp = document.getElementById("force_mdp")
    const errorMsg = document.getElementById("error-msg")
    const confirmermdp = document.getElementById('confirmermdp')
    const validateForm = document.getElementById('validateForm')

mdp.addEventListener("input", function () {
const value = mdp.value;
const valuedeux = confirmPassword.value;
let force = "faible";
let color = "red";

if (value.length === 0 && valuedeux.length === 0) {
    forceMdp.textContent = '';
    confirmermdp.textContent = '';
    return;
  }

  if (value.length === 0) {
    forceMdp.textContent = '';
  }

  if (valuedeux.length === 0) {
    confirmermdp.textContent = '';
  }


if (value.length >= 8 && /[\W_]/.test(value) && /[A-Z]/.test(value) && /[a-z]/.test(value) && /\d/.test(value)){
    force = "Mot de passe valider";
    color = "green";
}else if(value.length >= 8){
    force = "Moyen";
    color = "orange";
}
forceMdp.textContent = "Sécuriter du mot de passe:" + force;
forceMdp.style.color = color;
});

confirmPassword.addEventListener("input", function () {
    if (confirmPassword.value === mdp.value) {
        confirmermdp.textContent = "Les mots de passe correspondent.";
        confirmermdp.style.color = "green";
    } else {
        confirmermdp.textContent = "Les mots de passe ne correspondent pas.";
        confirmermdp.style.color = "red";
    }
});

validateForm.addEventListener("submit", function(event){
    let isValid = true;
    if (!(mdp.value.length >= 8 && /[A-Z]/.test(mdp.value) && /\d/.test(mdp.value) && /[\W_]/.test(mdp.value))) {
        isValid = false;
        alert("Le mot de passe doit être fort (au moins 8 caractères, avec des majuscules, des chiffres et des caractères spéciaux).");
    }

    if (mdp.value !== confirmPassword.value) {
        isValid = false;
        alert("Les mots de passe ne correspondent pas.");
    }

    if (!isValid) {
        event.preventDefault();
    }
});