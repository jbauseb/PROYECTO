/**CODIGO DE VALIDACIONES DE USUARIO
 * FOrmulario de búsqueda de usuario 
*/
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("searchForm");

    form.addEventListener("submit", function (event) {
        let isValid = true;

        //Validación del id (números positivos)
        const idEmpleado = document.getElementById("id_empleado");
        if (idEmpleado.value !== "" && (!/^\d+$/.test(idEmpleado.value) || parseInt(idEmpleado.value) <= 0)) {
            idEmpleado.style.backgroundColor = "#f2f278";//Cambia el fondo
            alert("El Id debe ser un número");
            isValid = false;
        }

        //Validación del nombre (letras y espacios)
        const nombreEmpleado = document.getElementById("nombre_empleado");
        if (nombreEmpleado.value !== "" && !/^[a-zA-ZÀ-ÿ\s]+$/.test(nombreEmpleado.value)) {
            nombreEmpleado.style.backgroundColor = "#f2f278";
            alert("El nombre solo puede contener letras y espacios");
            isValid = false;
        }

        //Validación del DNI (8 números + 1 letra)
        const dni = document.getElementById("dni_search");
        if (dni.value !== "" && !/^\d{8}[A-Za-z]$/.test(dni.value)) {
            dni.style.backgroundColor = "#f2f278";
            alert("El DNI debe tener 8 números seguidos de una letra");
            isValid = false;
        }

        //Si alguna validación falla, se detiene el envío del formulario
        if (!isValid) {
            event.preventDefault();
        }
    });
});

//FORMULARIO ALTA DE USUARIO
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("addForm");
    const nombre = document.getElementById("nombre");
    const dni = document.getElementById("dni");
    const usuario = document.getElementById("usuario");
    const email = document.getElementById("email");
    const telefono = document.getElementById("telefono");
    const password = document.getElementById("password");
    const confirm_password = document.getElementById("confirm_password");
    let isValid = true;

    //expresiones regulares
    const dniRegex = /^[0-9]{8}[A-Za-z]$/i;//8 dígitos y una letra
    const nombreRegex = /^[a-zA-ZÀ-ÿ\s,]+$/;//Letras y coma
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;//formato email
    const usuarioRegex = /^[a-zA-Z0-9]+$/;//letras y dígitos
    const telefonoRegex = /^[6789]\d{8}$/;//empieza ppor 6,7,8 o 9 más 8 dígitos más

    //Se muestra error debajo del campo
    function showError(input, message) {
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains("error-message")) {
            errorDiv = document.createElement("div");
            errorDiv.className = "error-message";
            input.parentNode.appendChild(errorDiv);
        }
        errorDiv.innerText = message;
        input.style.backgroundColor = "#f2f278"; //cambia el fondo
    }

    //Se quita error cuando es válido
    function clearError(input) {
        let errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains("error-message")) {
            errorDiv.remove();
        }
        input.style.backgroundColor = ""; //fondo original
    }

    //Validación en tiempo real
    function validateField(input, regex, errorMessage) {
        input.addEventListener("input", function () {
            if (regex.test(input.value) || input.value === "") {
                clearError(input);
                isValid = true;
            } else {
                showError(input, errorMessage);
                isValid = false;
            }
        });
    }
    //Validación confirmar password
    function validateConfirmPassword(a, b) {
        if (a.value === b.value) {
            isValid = true;
        } else {
            isValid = false;
            alert("Las contraseñas no coinciden.");
        }
    }
    //Se envía el formulario si todo es válido
    form.addEventListener("submit", function (event) {
        //Comprobamos ambas contraseñas
        validateConfirmPassword(password, confirm_password);
        // Si alguna validación falla, se detiene el envío del formulario
        if (!isValid) {
            console.log("no valido");
            event.preventDefault();
        }
    });

    //Mensajes de error de cada campo
    validateField(dni, dniRegex, "El DNI debe tener 8 números seguidos de una letra");
    validateField(nombre, nombreRegex, "El nombre solo puede contener letras, espacios y comas");
    validateField(email, emailRegex, "Introduce un correo válido (ej: usuario@dominio.com)");
    validateField(usuario, usuarioRegex, "El usuario solo puede contener letras y números");
    validateField(telefono, telefonoRegex, "El teléfono debe tener 9 dígitos y empezar por 6, 7, 8 o 9");

    //Funciones de PASSWORD
    //Alterna la visibilidad de la contraseña
    document.getElementById("alternaPassword").addEventListener("click", function () {
        const passwordField = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");

        // Cambiar tipo de input entre 'password' y 'text'
        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }

    });
    //Alterna la visibilidad de la confirmación de contraseña
    document.getElementById("alternaConfirmPassword").addEventListener("click", function () {
        const confirmPasswordField = document.getElementById("confirm_password");
        const eyeConfirmIcon = document.getElementById("eyeConfirmIcon");

        if (confirmPasswordField.type === "password") {
            confirmPasswordField.type = "text";
            eyeConfirmIcon.classList.remove("fa-eye");
            eyeConfirmIcon.classList.add("fa-eye-slash");
        } else {
            confirmPasswordField.type = "password";
            eyeConfirmIcon.classList.remove("fa-eye-slash");
            eyeConfirmIcon.classList.add("fa-eye");
        }
    });

});