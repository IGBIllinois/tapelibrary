<?php
class report {

        public static function get_heirarchy($db, $object_list, $level=0) {

            $data= array();
            $this_row = array();

                for($i=0; $i<$level; $i++) {
                    $this_row[] = "";
                }
                $headers = array_merge($this_row, array("Name","Type","Backupset","Tape Label", "Location"));
                $data[] = $headers;

            foreach($object_list as $object) {

                $id = $object->get_id();
                $tape_library_object = new tape_library_object($db, $id);

                $data_row = array_merge($this_row, array($tape_library_object->get_label(), $tape_library_object->get_type_name(), $tape_library_object->get_backupset_name(), $tape_library_object->get_tape_label(), $tape_library_object->get_container_name()));

                $data[] = $data_row;
                $object = new tape_library_object($db, $id);
                $children = $object->get_children($id);
                if(count($children) > 0) {
                    $data[] = array();

                    $data = array_merge($data, report::get_heirarchy($db, $children, (1+$level)));
                    $data[] = array();

                }

            }
            return $data;
            }

}
?>
