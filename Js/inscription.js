const mdp = document.getElementById("password");
        const confirmPassword = document.getElementById("confirm-password");
        const forceMdp = document.getElementById("force_mdp");
        const confirmermdp = document.getElementById('confirmermdp');
        const validateForm = document.getElementById('validateForm');
        const nom = document.getElementById('nom');
        const nomErreur = document.getElementById("nomErreur");



        const checks = [
            [/.{8,}/, "caracteres"],
            [/[a-z]/, "min"],
            [/[A-Z]/, "maj"],
            [/[0-9]/, "chiffre"],
            [/[!@#$%^&*(),.?":{}|<>]/, "special"]
        ];
    
        mdp.addEventListener("input", () => {
            checks.forEach(([regex, id_liste_obligation]) => {

                const element = document.getElementById(id_liste_obligation);
                const isValid = regex.test(mdp.value);
                element.classList.toggle("valid", isValid);
                element.classList.toggle("invalid", !isValid);
            });
        });

        confirmPassword.addEventListener("input", function () {
            if (mdp.value === "" || confirmPassword.value === "") {
                confirmermdp.textContent = ""; // Efface le message si l'un des champs est vide
            } else if (confirmPassword.value === mdp.value) {
                confirmermdp.textContent = "Les mots de passe correspondent.";
                confirmermdp.style.color = "green";
            } else {
                confirmermdp.textContent = "Les mots de passe ne correspondent pas.";
                confirmermdp.style.color = "red";
            }
        });

        nom.addEventListener("input", function () {
            if (nom.value.length < 2) {
                nomErreur.textContent = "Le nom doit comporter au moins 2 caractères.";
            } else {
                nomErreur.textContent = ""; // Efface le message si c'est valide
            }
        });
        // validateForm.addEventListener("submit", function(event) {
     

        //     let isValid = true;
        //     const value = mdp.value;

        //     if (!(value.length >= 8 && /[A-Z]/.test(value) && /[a-z]/.test(value) && /\d/.test(value) && /[\W_]/.test(value))) {
        //         isValid = false;
        //         alert("Le mot de passe doit respecter tous les critères.");
        //     }

        //     if (mdp.value !== confirmPassword.value) {
        //         isValid = false;
        //         alert("Les mots de passe ne correspondent pas.");
        //     }

        //     if (!isValid) {
        //         event.preventDefault();
        //     }
        // });



