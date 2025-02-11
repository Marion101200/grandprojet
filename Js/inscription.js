
    const mdp = document.getElementById("password")
    const confirmPassword = document.getElementById("confirm-password")
    const forceMdp = document.getElementById("force_mdp")
    const errorMsg = document.getElementById("error-msg")

mdp.addEventListener("input", function () {
const value = mdp.value;
let force = "faible";
let color = "red";

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



// if (mdp !== confirmPassword) {
//     errorMsg.textContent = "Les mots de passe ne correspondent pas.";
//     errorMsg.style.color = "red";
//     return false;
// }
// return true;