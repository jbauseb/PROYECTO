/**Función para routes/index
 * 
 * Función para mostrar la tabla correspondiente y ocultar las demás
*/
function mostrarTabla(tipo) {
    //Oculta todas las tablas
    document.getElementById('tabla_origen').style.display = 'none';
    document.getElementById('tabla_transito').style.display = 'none';
    document.getElementById('tabla_destino').style.display = 'none';

    //Muestra la tabla seleccionada
    document.getElementById('tabla_' + tipo).style.display = 'block';
}

/**Función para routes/edit
 * Espera a que el DOM esté completamente cargado antes de ejecutar el script
*/
document.addEventListener("DOMContentLoaded", function () {
    //Obtiene referencias a los campos del formulario
    const origen = document.getElementById("origen");
    const destino = document.getElementById("destino");
    const fechaSalida = document.getElementById("fecha_salida");
    const horaSalida = document.getElementById("hora_salida");
    const fechaLlegada = document.getElementById("fecha_llegada");
    const horaLlegada = document.getElementById("hora_llegada");
    const form = document.querySelector("form");

    //Función para mostrar un mensaje de error debajo de un campo
    function showError(input, message) {
        let errorDiv = input.nextElementSibling;//Verifica si ya hay un mensaje de error
        if (!errorDiv || !errorDiv.classList.contains("error-message")) {
            //Si no existe, se crea un nuevo div para mostrar el error
            errorDiv = document.createElement("div");
            errorDiv.className = "error-message text-danger small mt-1";
            input.parentNode.appendChild(errorDiv);
        }
        errorDiv.innerText = message;//Inserta el mensaje de error
        input.style.backgroundColor = "#f2f278"; //Resalta el campo con un color de advertencia
    }

    //Función para eliminar mensajes de error y restaurar el color del campo
    function clearError(input) {
        let errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains("error-message")) {
            errorDiv.remove();//Elimina el mensaje de error si existe
        }
        input.style.backgroundColor = ""; //Restaura color original
    }

    //Función para validar los campos del formulario
    function validateFields() {
        let isValid = true;//Variable de control

        //Valida que el origen y el destino sean diferentes
        if (origen.value === destino.value) {
            showError(destino, "El destino debe ser distinto al origen");
            isValid = false;
        } else {
            clearError(destino);
        }

        //Convierte las fechas y horas de salida y llegada a objetos Date
        let salida = new Date(fechaSalida.value + "T" + horaSalida.value);
        let llegada = new Date(fechaLlegada.value + "T" + horaLlegada.value);

        //Valida que la fecha y hora de llegada sean posteriores a la de salida
        if (llegada <= salida) {
            showError(fechaLlegada, "La fecha y hora de llegada deben ser posteriores a la fecha y hora de salida");
            showError(horaLlegada, "La hora de llegada debe ser posterior a la hora de salida");
            isValid = false;
        } else {
            clearError(fechaLlegada);
            clearError(horaLlegada);
        }

        return isValid;//Devuelve si la validación fue exitosa o no
    }

    //Agrega eventos de validación en tiempo real a cada campo del formulario
    [origen, destino, fechaSalida, horaSalida, fechaLlegada, horaLlegada].forEach(input => {
        input.addEventListener("input", validateFields);
    });

    //Valida el formulario antes de enviarlo
    form.addEventListener("submit", function (event) {
        if (!validateFields()) {
            event.preventDefault();//Evita que el formulario se envíe si hay errores
        }
    });


});
