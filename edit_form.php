<?php

class block_participantschart_edit_form extends block_edit_form {

    public static $type = array("totalparticipants" => "Total Participants per Course",
                                        "totalcourse" => "Total Course that Participants");
   
    protected function specific_definition($form) {

        $form->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $form->addElement('text', 'config_limit', get_string('chart_limit', 'block_participantschart'));
        $form->setType('config_limit', PARAM_INT);

        
        // $form->addElement('select', 'config_charttype', get_string('chart_type', 'block_participantschart'), $type);

    }
}
