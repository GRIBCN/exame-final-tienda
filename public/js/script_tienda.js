// script_tienda.js
import SelectElementJS from './SelectElementJS.js';

// Ejecuta cuando el DOM esté completamente construido
document.addEventListener('DOMContentLoaded', function() {
    // Obtener referencias a las etiquetas de las vistas

    // Referencia al contenedor de los mensajes
    const mensajeStatus = document.getElementById('mensajeStatus');

    // El botton FILTRAR
    const ejecutarTiendaFilterLinks = document.getElementById('idEjecutarTiendaFilter');

    // El botton RESET
    const resetTiendaFilterLinks = document.getElementById('idResetTiendaFilter');

    // Verificar si estamos en la página de TIENDA
    if (document.getElementById('idTiendaPage')) {
        // Obtener referencia a la pagina
        const vistaTiendaLinks = document.getElementById('idTiendaPage');
        
        if (vistaTiendaLinks.dataset.filterConsulta) {
            // Obtener la consulta de búsqueda seleccionada
            let sqlConsulta = vistaTiendaLinks.dataset.filterConsulta;
           
            const mensajeConsulta = `
                <strong> "${sqlConsulta}" </strong>
            `;

            mensajeStatus.innerHTML = htmlMessageZone('success', mensajeConsulta);
        }

        // Obtener la opción de búsqueda seleccionada
        const searchOption = document.querySelector('input[name="searchOption"]:checked');
        generarSelectFiltrar(searchOption);
    }


    // Definimos metodo Click() de botton FILTRAR
    if (ejecutarTiendaFilterLinks) {
        ejecutarTiendaFilterLinks.addEventListener("click", function() {
            filtrarTienda(this);
        });
    }

    // Definimos metodo Click() de botton RESET
    if (resetTiendaFilterLinks) {
        resetTiendaFilterLinks.addEventListener("click", function() {
            resetTienda();
        });
    }

    // Escuchar cambios en los radio buttons
    document.querySelectorAll('input[name="searchOption"]').forEach(radio => {
        radio.addEventListener('change', function() {
            generarSelectFiltrar(this);
        });
    });
});

// Generar el select de filtro según la opción seleccionada
function generarSelectFiltrar(radio) {
    // Referencia al contenedor de los mensajes
    const mensajeStatus = document.getElementById('mensajeStatus');
    // Referencia de <div> del <select>
    const selectFiltrar = document.getElementById('idDynamicDivSelectFilter');

    // Limpiar el select
    selectFiltrar.innerHTML = '';

    // Obtener los valores de la opción seleccionada
    const sqlConsult = radio.dataset.sqlConsult;
    const sqlSelect = radio.dataset.sqlSelect;
    const field = radio.value;

    // Validar que se hyan selecionado Precio
    if (field == 'precio') {
        // Generar los elementos del select
        // Definimos la array de operadores de condiciones
        const data = {
            datos: [
                { f_field: '=' },
                { f_field: '<>' },
                { f_field: '>' },
                { f_field: '<' },
                { f_field: '>=' },
                { f_field: '<=' }
            ]
        };

        const selectHTML = renderSelectFilter('idDynamicSelectFilter-precio', 'dynamicSelectFilter-precio', 'form-select', true, true, data.datos);

        // Generamos el codigo HTML de la etiqueta <input> para el Precio
        const inputHTML = `
            <input 
                type="number" 
                class="form-control" 
                id="idPrecio" 
                name="precio" 
                step="0.01" 
                min="0" 
                placeholder="Precio"
            >
        `;

        selectFiltrar.innerHTML = selectHTML + inputHTML;
    } else {
        // Enviar solicitud AJAX al servidor para ejecutar la consulta seleccionada
        fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                'views': 'tienda',
                'crud': 'select-filter',
                'r': 'tienda-filter-select',
                'field': field
            })
        })
        .then(response => response.json())  // Procesar la respuesta del servidor
        .then(data => {
            // alert("Parámetros: " + JSON.stringify(data.datos));
            if (data.success) {
                // Generar los elementos del select
                // Llamar a la función para generar el select usando los datos obtenidos
                const selectHTML = renderSelectFilter('idDynamicSelectFilter', 'dynamicSelectFilter', 'form-select', true, true, data.datos);
                selectFiltrar.innerHTML = selectHTML;
            } else {
                // Mostrar mensaje de error si no se pudo generar SELECT correctamente
                const mensajeError = `
                    <strong>Error: </strong> 
                    ${data.msg01} 
                    <strong> "${data.msg02}" </strong>
                    ${data.msg03}
                `;
                mensajeStatus.innerHTML = htmlMessageZone('danger', mensajeError);
            }
        })
        .catch(error => {
            // Mostrar mensaje de error si no se pudo completar la solicitud AJAX
            description = 'Error al intentar generar SELECT ';
            const mensajeError = `
                <strong>Error: </strong> 
                ${description}
                <strong> "${titleTienda}" </strong>
                intente nuevamente más tarde.
            `;
            mensajeStatus.innerHTML = htmlMessageZone('danger', mensajeError);
        });
    }
}

// Función para generar el <select> con los datos proporcionados usando SelectElementJS
function renderSelectFilter(id, name, className, required, multiple, options) {
    // Crear una nueva instancia de SelectElementJS
    const selectElement = new SelectElementJS(id, name, className, required);

    // Establecer la opción por defecto
    selectElement.setDefaultOption('Seleccionar...', 'default');


    // Añadir las opciones al <select> usando el método addOption
    options.forEach(option => {
        selectElement.addOption(option.f_field, option.f_field);
    });

    // Renderizar y devolver el HTML del <select>
    return selectElement.render();
}

function filtrarTienda (boton) {
    // Prevenir el envío normal del formulario
    //event.preventDefault();

    // Referencia al contenedor de los mensajes
    const mensajeStatus = document.getElementById('mensajeStatus');
    const tablaContainer = document.getElementById('tablaContainer'); // Contenedor donde se encuentra la tabla.

    // Obtener los datos consulta indicada y el valor introducido
    // Obtener la opción de búsqueda seleccionada
    const searchRadio = document.querySelector('input[name="searchOption"]:checked');

    // Obtener el valor del Radio seleccionado
    const searchField = searchRadio.value;
 
    // Obtener los datos del valor introducido
    const searchSelect = document.getElementById(
        (searchField === 'precio') ? 'idDynamicSelectFilter-precio' : 'idDynamicSelectFilter'
    );

    // Obtener la consulta seleccionada y el valor introducido para la búsqueda
    const searchConsulta = searchRadio.dataset.sqlConsult;
    const searchValue = searchSelect.value;

    let precio = 0;
    let isPrecio = '0';
    // Validar que se hyan selecionado Precio
    if (searchField == 'precio') {
        // Referencia al contenedor del input del Precio
        const inputPrecioLink = document.getElementById('idPrecio');
        precio = inputPrecioLink.value;
        isPrecio = '1';
    }

    // Enviar solicitud AJAX al servidor para ejecutar la consulta seleccionada
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            'views': 'tienda',
            'crud': 'filter',
            'r': 'tienda-filter',
            'consulta': searchConsulta,
            'field': searchField,
            'value': searchValue,
            'precio': precio,
            'isPrecio': isPrecio // Convertir booleano a cadena
        })
    })
    .then(response => response.json())  // Procesar la respuesta del servidor
    .then(data => {
        // alert("Parámetros: " + JSON.stringify(data.datos));
        if (data.success) {
            // Actualizar la tabla con el nuevo HTML recibido
            tablaContainer.innerHTML = data.tablaHTML;

            // Mostrar la consulta ejecutada y mensaje de éxito
            const mensajeSuccess = `
                <strong>Éxito: </strong> 
                ${data.msg01} 
                <strong> "${data.msg02}" </strong>
                ${data.msg03}
            `;
            mensajeStatus.innerHTML = htmlMessageZone('success', mensajeSuccess);
        } else {
            // Mostrar mensaje de error si no se pudo generar SELECT correctamente
            const mensajeError = `
                <strong>Error: </strong> 
                ${data.msg01} 
                <strong> "${data.msg02}" </strong>
                ${data.msg03}
            `;
            mensajeStatus.innerHTML = htmlMessageZone('danger', mensajeError);
        }
    })
    .catch(error => {
        // Mostrar mensaje de error si no se pudo completar la solicitud AJAX
        description = 'Error al intentar generar SELECT ';
        const mensajeError = `
            <strong>Error: </strong> 
            ${description}
            <strong> "${searchConsulta}" </strong>
            intente nuevamente más tarde.
        `;
        mensajeStatus.innerHTML = htmlMessageZone('danger', mensajeError);
    });
}

function resetTienda() {
    // Reiniciar la pagina
    window.location.href = 'index.php?r=home';
}