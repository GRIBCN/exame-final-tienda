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


            case 'movies':
                if( !isset( $datos_post['r'] ) )  {
                    $controller->load_view('movies');
                }
                else if( $datos_post['r'] == 'movies-add' ) {
                    $controller->load_view('movies-add');
                }
                else if( $datos_post['r'] == 'movies-edit' ) {
                    $controller->load_view('movies-edit');
                }
                else if( $datos_post['r'] == 'movies-delete' )  {
                    $controller->load_view('movies-delete');
                }
                else if( $datos_post['r'] == 'movies-filter' )  {
                    $controller->load_view('movies-filter');
                }
                else if( $datos_post['r'] == 'movies-filter-select' )  {
                    $this->renderSelectFilter($datos_post['field']);  // Renderiza el modal de Categorías
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
        $movies_controller = new MoviesController();

        // ETIQUETA <SELECT>
        $result = $movies_controller->getValueFields($field);

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

}

?>