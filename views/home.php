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
                </div>

                <table class="table-smartgrid w-100 mt-3" style="hight: 100;">
					<thead class="bg-secondary">
						<tr>
        ';

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
        ';

        print($template_tienda);
    }
?>