<?php
    class TiendaController extends Model_crud {
        protected $table = 'productos_fabricante';              // Especifica la tabla a usar
        protected $column_id = 'rowid';                         // Especifica la columna clave a usar

        // Funciones específicas
        // Consultamos la lista de los valores únicos de la columna indicada
        public function getValueFields($field) {
            $query = "CALL prodfabr_field_DISTINCT_START(?)";
            return $this->execute_prepared_select($query, 's', [$field]);
        }

        // Verifica si la filla EXISTE
        public function filterTienda($query, $valor) {

            return $this->execute_prepared_select($query, 's', ['%' . $valor . '%']);
        }

        // Consultar por precio con operador de comparacion predeterminado
        public function filterTiendaPrecio($query, $operador, $precio) {
            // Decodificar el operador en caso de que venga como entidad HTML
            $operador = html_entity_decode($operador);

            // Construir la consulta segura con el operador validado
            $finalQuery = $query . " " . $operador . " ?";

            return $this->execute_prepared_select($finalQuery, 'd', [$precio]);
        }
    }
?>