<?php
    /*
    $template = '
        <article class="item text-center">
            <h2 class="p1">
                "' . APP_SALUDO . '"
            </h2>
            "' . APP_TITLE . '"
        </article>
    ';
    
    printf(
        $template
    );
    */

    // Instanciamos la clase de FiltrarDatos para filtrar los datos del GET
    $filtro = new FiltrarDatos();
    $datos_get = $filtro->Filtrar($_GET);
    

    print('
        <div
            id="idTiendaPage"
            data-id-page = "' . isset($datos_get['r']) . '"
            data-filter-consulta = "' . (isset($datos_get['consulta']) ? $datos_get['consulta'] : '') . '"
            data-filter-valor = "' . (isset($datos_get['valor']) ? $datos_get['valor'] : '') . '"
        >
            <h2 class="text-center">'
                . APP_LOGO_HOME .
            '</h2>
        </div>

    ');

    // Instanciamos la clase de Modelo de Tienda para consultar los datos
    $tienda_controller = new TiendaController();
 
    if (isset($datos_get['consulta']) && isset($datos_get['valor']) && !empty($datos_get['consulta']) && !empty($datos_get['valor'])) {
        $tienda = $tienda_controller->filterTienda($datos_get['consulta'], $datos_get['valor']);
    } else {
        $tienda = $tienda_controller->get();
    }

    if( empty($tienda) ) {
        print( '
            <div class="container">
                <p class="item  error">No hay regisytos</p>
            </div>
        ');
    } else {
	    $template_tienda = '
		    <div class="card card-smartgrid">
                <div class="card-header card-header-smartgrid bg-dark text-white text-center">
                    <h3 class="card-title">Tienda</h3>
                </div>
                <div 
                    class="card-body card-body-smartgrid p-3 d-flex justify-content-between align-items-center w-100"
                        id="idActionButtonsTopTienda"
                        name="actionButtonsTopTienda"
                >
                    <!-- Inputs Radio de la izquierda -->
                    <div class="form-group text-start style="justify-content: flex-left;"">
                        <form>
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="searchOption" 
                                    id="searchNombre_fabricante" 
                                    value="nombre_fabricante"
                                    data-sql-consult="SELECT * FROM productos_fabricante WHERE nombre_fabricante LIKE ?"
                                    data-sql-select="SELECT DISTINCT nombre_fabricante FROM productos_fabricante ORDER BY nombre_fabricante"
                                    checked
                                >
                                <label class="form-check-label" for="searchNombre_fabricante">
                                    Búsqueda por el Nombre de Fabricante
                                </label>
                            </div>
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="searchOption" 
                                    id="searchNombre_producto" 
                                    value="nombre_producto"
                                    data-sql-consult="SELECT * FROM productos_fabricante WHERE nombre_producto LIKE ?"
                                    data-sql-select="SELECT DISTINCT nombre_producto FROM productos_fabricante ORDER BY nombre_producto"
                                >
                                <label class="form-check-label" for="searchNombre_producto">
                                    Búsqueda por Tipo de Producto
                                </label>
                            </div>
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="searchOption" 
                                    id="searchPrecio" 
                                    value="precio"
                                    data-sql-consult="SELECT * FROM productos_fabricante WHERE precio"
                                    data-sql-select="SELECT DISTINCT precio FROM productos_fabricante ORDER BY precio"
                                >
                                <label class="form-check-label" for="searchPrecio">
                                    Búsqueda por Precio
                                </label>
                            </div>

                        </form>
                    </div>
                    <!-- Selectores de la parte central -->
                    <div class="form-group text-center" id="idDynamicDivSelectFilter" style="justify-content: flex-center;">
                    </div>
                    <!-- Botones FILTRAR y RESET de la parte derecha -->
                    <div class="close-button" id="idActionButtonsTopRight" style="justify-content: flex-end;">
                        <div class="button-container" id="idActionTiendaButtonsFilterContainer">
                            <button class="btn-secondary-cancel" type="reset" value="NO" id="idResetTiendaFilter">
                                Reset
                            </button>
                            <button 
                                id="idEjecutarTiendaFilter"
                                class="btn-primary-confirm" 
                                type="submit" 
                                value="SI"
                                onclick="filterTienda(this)"
                            >
                                Filtrar
                            </button>
                            <input type="hidden" name="crud" value="filter">
                            <input type="hidden" name="r" value="tienda-filter">
                            <input type="hidden" name="rowid" value="">
                        </div>
                    </div>
                </div>
                <div id="tablaContainer">
                <table class="table-smartgrid w-100 mt-3" style="hight: 100;">
					<thead class="bg-secondary">
						<tr>
        ';

/*
                        <div class="mb-3 mt-3">
                            <div class="form-group" id="idDynamicDivSelectFilter">

                            </div>
                        </div>
*/

/*
                    <!-- Botones a la izquierda -->
                    <div class="col-lg-3 action-buttons d-flex justify-content-start" id="idActionButtonsTopLeftTienda">
                        <form method="POST">
                            <input type="hidden" name="r" value="Tienda-add">
                            <button 
                                class="btn btn-action" 
                                type="submit"
                            >
                                <span class="material-icons">add</span>
                            </button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="r" value="tienda-edt">
                            <button class="btn btn-action" type="submit">
                                <span class="material-icons">edit</span>
                            </button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="r" value="tienda-del">
                            <button class="btn btn-action" type="submit">
                                <span class="material-icons">delete</span>
                            </button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="r" value="tienda-filter"> <!-- Acción para el filtro -->
                            <button class="btn btn-action" type="submit">
                                <span class="material-icons">filter_list</span> <!-- Ícono de filtro -->
                            </button>
                        </form>
                    </div>
*/
        // LA CABECERA DE LA TABLA
        foreach ($tienda[0] as $key_row => $row) {
            $template_tienda .= '
                <th class="th-smartgrid text-center">
                    '. ucfirst($key_row). '
                </th>
            ';
        }
        $template_tienda .= '
                            <th class="th-smartgrid text-center"></th>
						</tr>
					</thead>
        ';

        // EL CUERPO DE LA TABLA
        foreach ($tienda as $key_row => $row) { 
            $template_tienda .= '
                <tr class="tr-smartgrid">';
                foreach ($row as $key_cell => $cell) {
                    $template_tienda .= '<td class="td-smartgrid">'. $cell. '</td>';
                }
                $template_tienda .= '
                    <td class="td-smartgrid">
                        <div class="action-buttons d-flex">
                            <form class="action-buttons" method="POST">
                                <input type="hidden" name="r" value="tienda-edit">
                                <input type="hidden" name="rowid" value="' . $row['rowid'] . '">
                                <input type="hidden" name="pk_fabricante" value="' . $row['pk_fabricante'] . '">
                                <input type="hidden" name="nombre_fabricante" value="' . $row['nombre_fabricante'] . '">
                                <input type="hidden" name="pk_producto" value="' . $row['pk_producto'] . '">
                                <input type="hidden" name="nombre_producto" value="' . $row['nombre_producto'] . '">
                                <input type="hidden" name="precio" value="' . $row['precio'] . '">
                                <button class="btn btn-action" type="submit">
                                    <span class="material-icons">edit</span>
                                </button>
                            </form>
                            <form class="action-buttons" method="POST">
                                <input type="hidden" name="r" value="tienda-delete">
                                <input type="hidden" name="imdb_id" value="' . $row['rowid'] . '">
                                <input type="hidden" name="title" value="' . $row['nombre_fabricante'] . ' - ' . $row['nombre_producto'] . '">
                                <button class="btn btn-action" type="submit">
                                    <span class="material-icons">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            ';
        }

        $template_tienda .= '
                </table>
                </div>
            </div>
        ';

        print($template_tienda);
    }
?>