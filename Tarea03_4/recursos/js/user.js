// /**CODIGO DE VALIDACIONES DE USUARIO

document.addEventListener("DOMContentLoaded", function () {
    // Validación del formulario de búsqueda
    const searchForm = document.getElementById("searchForm");
    if (searchForm) {
        searchForm.addEventListener("submit", function (event) {
            let isValid = true;

            const idEmpleado = document.getElementById("id_empleado");
            if (idEmpleado.value !== "" && (!/^\d+$/.test(idEmpleado.value) || parseInt(idEmpleado.value) <= 0)) {
                idEmpleado.style.backgroundColor = "#f2f278";
                alert("El Id debe ser un número positivo");
                isValid = false;
            }

            const nombreEmpleado = document.getElementById("nombre_empleado");
            if (nombreEmpleado.value !== "" && !/^[a-zA-ZÀ-ÿ\s]+$/.test(nombreEmpleado.value)) {
                nombreEmpleado.style.backgroundColor = "#f2f278";
                alert("El nombre solo puede contener letras y espacios");
                isValid = false;
            }

            const dni = document.getElementById("dni_search");
            if (dni.value !== "" && !/^\d{8}[A-Za-z]$/.test(dni.value)) {
                dni.style.backgroundColor = "#f2f278";
                alert("El DNI debe tener 8 números seguidos de una letra");
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    // Validación del formulario de alta
    const addForm = document.getElementById("addForm");
    if (addForm) {
        const nombre = document.getElementById("nombre");
        const dni = document.getElementById("dni");
        const usuario = document.getElementById("usuario");
        const email = document.getElementById("email");
        const telefono = document.getElementById("telefono");
        const password = document.getElementById("password");
        const confirm_password = document.getElementById("confirm_password");

        const dniRegex = /^[0-9]{8}[A-Za-z]$/i;
        const nombreRegex = /^[a-zA-ZÀ-ÿ\s,]+$/;
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        const usuarioRegex = /^[a-zA-Z0-9]+$/;
        const telefonoRegex = /^[6789]\d{8}$/;

        function showError(input, message) {
            let errorDiv = input.nextElementSibling;
            if (!errorDiv || !errorDiv.classList.contains("error-message")) {
                errorDiv = document.createElement("div");
                errorDiv.className = "error-message";
                input.parentNode.appendChild(errorDiv);
            }
            errorDiv.innerText = message;
            input.style.backgroundColor = "#f2f278";
        }

        function clearError(input) {
            let errorDiv = input.nextElementSibling;
            if (errorDiv && errorDiv.classList.contains("error-message")) {
                errorDiv.remove();
            }
            input.style.backgroundColor = "";
        }

        function validateField(input, regex, errorMessage) {
            input.addEventListener("input", function () {
                if (regex.test(input.value) || input.value === "") {
                    clearError(input);
                } else {
                    showError(input, errorMessage);
                }
            });
        }

        addForm.addEventListener("submit", function (event) {
            let formIsValid = true;

            if (!dniRegex.test(dni.value)) {
                showError(dni, "El DNI debe tener 8 números seguidos de una letra");
                formIsValid = false;
            } else {
                clearError(dni);
            }

            if (!nombreRegex.test(nombre.value)) {
                showError(nombre, "El nombre solo puede contener letras, espacios y comas");
                formIsValid = false;
            } else {
                clearError(nombre);
            }

            if (!emailRegex.test(email.value)) {
                showError(email, "Introduce un correo válido (ej: usuario@dominio.com)");
                formIsValid = false;
            } else {
                clearError(email);
            }

            if (!usuarioRegex.test(usuario.value)) {
                showError(usuario, "El usuario solo puede contener letras y números");
                formIsValid = false;
            } else {
                clearError(usuario);
            }

            if (!telefonoRegex.test(telefono.value)) {
                showError(telefono, "El teléfono debe tener 9 dígitos y empezar por 6, 7, 8 o 9");
                formIsValid = false;
            } else {
                clearError(telefono);
            }

            if (password.value !== confirm_password.value) {
                alert("Las contraseñas no coinciden.");
                formIsValid = false;
            }

            if (!formIsValid) {
                event.preventDefault();
            }
        });

        // Validación en tiempo real
        validateField(dni, dniRegex, "El DNI debe tener 8 números seguidos de una letra");
        validateField(nombre, nombreRegex, "El nombre solo puede contener letras, espacios y comas");
        validateField(email, emailRegex, "Introduce un correo válido (ej: usuario@dominio.com)");
        validateField(usuario, usuarioRegex, "El usuario solo puede contener letras y números");
        validateField(telefono, telefonoRegex, "El teléfono debe tener 9 dígitos y empezar por 6, 7, 8 o 9");

        // Mostrar/ocultar contraseñas
        const alternaPasswordBtn = document.getElementById("alternaPassword");
        if (alternaPasswordBtn) {
            alternaPasswordBtn.addEventListener("click", function () {
                const eyeIcon = document.getElementById("eyeIcon");
                if (password.type === "password") {
                    password.type = "text";
                    eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
                } else {
                    password.type = "password";
                    eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
                }
            });
        }

        const alternaConfirmBtn = document.getElementById("alternaConfirmPassword");
        if (alternaConfirmBtn) {
            alternaConfirmBtn.addEventListener("click", function () {
                const eyeConfirmIcon = document.getElementById("eyeConfirmIcon");
                if (confirm_password.type === "password") {
                    confirm_password.type = "text";
                    eyeConfirmIcon.classList.replace("fa-eye", "fa-eye-slash");
                } else {
                    confirm_password.type = "password";
                    eyeConfirmIcon.classList.replace("fa-eye-slash", "fa-eye");
                }
            });
        }
    }
});
