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
    }
?>