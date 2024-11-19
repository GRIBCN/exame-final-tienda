<?php

class Router {
    public $route;

    public function __construct() {
        $filtro = new FiltrarDatos();

        // Filtrar los datos del GET y POST para evitar ataques de inyeccion
        $datos_get = $filtro->Filtrar($_GET);
        $datos_post = $filtro->Filtrar($_POST);

        $this->route = isset($datos_get['r']) 
    				? $datos_get['r'] 
    			: (isset($datos_post['r']) && isset($datos_post['views']) 
        			? $datos_post['views'] 
        		: 'home');

        $controller = new ViewController();     // Carga la pagina

        switch ($this->route) {
            case 'home':
                $controller->load_view('home');
                break;
            case 'tienda':
                // Generamos Selector
                if( $datos_post['r'] == 'tienda-filter-select' )  {
                    $this->renderSelectFilter($datos_post['field']);  // Renderiza el modal de Categorías
                }

                // Filtramos la tabla
                if( $datos_post['r'] == 'tienda-filter' )  {
                    $this->filtrarTabla($datos_post['consulta'], $datos_post['value'], $datos_post['precio'], $datos_post['field']);  // Filtramos la tabla
                }
                break;
            default:
                $controller->load_view('404');
                break;
        }
    }
    
    private function renderSelectFilter($field) {
        // Renderizar el <select> del formulario Filtrar
        // Implementamos proceder los datos de las llamadas AJAX
        $tienda_controller = new TiendaController();

        // ETIQUETA <SELECT>
        $result = $tienda_controller->getValueFields($field);

        if ($result) {
            // Ha consultado los valores únicos de la columna, devolvemos una respuesta JSON
            echo json_encode([
                'success' => true,
                'msg01' => 'La etiqueta SELCT de la columna ',
                'msg02' => $field,
                'msg03' => 'ha sido generada correctamente',
                'datos' => $result
            ]);
        } else {
            // Si ocurrió un error al intentar consultar los valores únicos de uan columna
            echo json_encode([
                'success' => false,
                'msg01' => 'Error al intentar consultar los valores únicos de la columna ',
                'msg02' => $field,
                'msg03' => '!'
            ]);
        }
        exit();

    }

    private function filtrarTabla($consulta, $valor, $precio, $field) {
        // Filtramos la tabla
        // Instanciamos la clase de Modelo de Tienda para consultar los datos
        $tienda_controller = new TiendaController();

        // Filtramos por precio
        if ($field === 'precio') {
            $tienda = $tienda_controller->filterTiendaPrecio($consulta, $valor, (float)$precio);
            $consulta_res = $consulta . ' ' . $valor . ' ' . $precio;
        } else {
            $tienda = $tienda_controller->filterTienda($consulta, $valor);
            $consulta_res = str_replace('?', '', $consulta) . ' %' . $valor . '%';
        }
        
        if ($tienda) {
            // Ha consultado los valores únicos de la columna, devolvemos una respuesta JSON
            echo json_encode([
                'success' => true,
                'msg01' => 'La consulta ',
                'msg02' => $consulta_res,
                'msg03' => 'ha sido ejecutada correctamente',
                'tablaHTML' => $this->generarTabla($tienda)
            ]);
        } else {
            // Si ocurrió un error al intentar consultar los valores únicos de uan columna
            echo json_encode([
                'success' => false,
                'msg01' => 'Error al intentar ejecutar la consulta ',
                'msg02' => $consulta_res,
                'msg03' => '!'
            ]);
        }
        exit();
    }

    function generarTabla($tienda) {
        // Generar la tabla actualizada
        $template_tienda = '';
        if (!empty($tienda)) {
            // Generar cabecera de la tabla
            $template_tienda .= '
                <table class="table-smartgrid w-100 mt-3" style="hight: 100;">
                    <thead class="bg-secondary">
                        <tr>';
            foreach ($tienda[0] as $key_row => $row) {
                $template_tienda .= '<th class="th-smartgrid text-center">' . ucfirst($key_row) . '</th>';
            }

            // Generar el cuerpo de la tabla
            foreach ($tienda as $row) {
                $template_tienda .= '<tr class="tr-smartgrid">';
                foreach ($row as $cell) {
                    $template_tienda .= '<td class="td-smartgrid">' . $cell . '</td>';
                }
                $template_tienda .= '
                    </tr>';
            }
            $template_tienda .= '</table>';
        } else {
            $template_tienda .= '<p>No se encontraron resultados.</p>';
        }
        return $template_tienda;
    }

}

?>