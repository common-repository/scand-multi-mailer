<?php

/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * Scand_Multi_Mailer_Provider_Repository class
 */

class Scand_Multi_Mailer_Provider_Repository
{
    private $table_name;

    function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'mm_providers';
    }

    public function create_table()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE ".$this->table_name." (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          data text,
          send_from varchar(255),
          send_from_name varchar(255),
          send_to varchar(255),
          message text,
          name varchar(255),
          subject varchar(255),
          `update` datetime,
          type_sender varchar(30),
          PRIMARY KEY  (id)
          ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public function update_provider(Scand_Multi_Mailer_Provider $provider )
    {
        global $wpdb;

        $answer = $wpdb->update(
            $this->table_name,
            array(
                "send_from" => $provider->get_send_from(),
                "send_from_name" => $provider->get_send_from_name(),
                "send_to" => $provider->get_send_to(),
                "message" => addslashes( $provider->get_message() ),
                "subject" => $provider->get_subject(),
                "name" => $provider->get_name(),
                "update" => current_time('mysql', 1),
                "data" => serialize( $provider->get_sender_data() ),
                "type_sender" => $provider->get_sender_name()
            ),
            array(
                "id" => $provider->get_id()
            )
        );

        return $answer;
    }

    public function insert_provider(Scand_Multi_Mailer_Provider $provider )
    {
        global $wpdb;

        $answer = $wpdb->insert(
            $this->table_name,
            array(
                "send_from" => $provider->get_send_from(),
                "send_from_name" => $provider->get_send_from_name(),
                "send_to" => $provider->get_send_to(),
                "message" => addslashes( $provider->get_message() ),
                "subject" => $provider->get_subject(),
                "name" => $provider->get_name(),
                "update" => current_time('mysql', 1),
                "data" => serialize( $provider->get_sender_data() ),
                "type_sender" => $provider->get_sender_name()
            )
        );

        return $answer;
    }

    public function get_provider_by_id($id , $registerSenders)
    {
        global $wpdb;
        $sql = "SELECT * FROM $this->table_name WHERE id=$id";
        $row = $wpdb->get_row( $sql );
        $provider = new Scand_Multi_Mailer_Provider();

        if( !empty( $row)) {
            $provider = new Scand_Multi_Mailer_Provider();
            $provider->set_id( $row->id );
            $provider->set_name( $row->name );
            $provider->set_message( $row->message );
            $provider->set_send_from( $row->send_from);
            $provider->set_send_from_name( $row->send_from_name );
            $provider->set_send_to( $row->send_to );
            $provider->set_subject( $row->subject );
            $provider->set_update( $row->update );
            $sender = self::initial_sender( $registerSenders, $row->type_sender );
            if( !empty( $sender ) ) {
                $data = unserialize( $row->data );
                if (!empty( $data ) ) {
                    $sender->save_param($data);
                }
                $provider->set_sender( $sender );
            }
        }

        return $provider;
    }

    public function get_list_providers($registerSenders )
    {
        global $wpdb;
        $sql = "SELECT * FROM $this->table_name ORDER BY $this->table_name.update DESC";
        $rows = $wpdb->get_results( $sql );
        $listProviders = array();

        if( !empty( $rows ) && is_array( $rows ) ) {
            foreach($rows as $row) {
                $provider = new Scand_Multi_Mailer_Provider();
                $provider->set_id( $row->id );
                $provider->set_name( $row->name );
                $provider->set_message( $row->message );
                $provider->set_send_from( $row->send_from);
                $provider->set_send_from_name( $row->send_from_name );
                $provider->set_send_to( $row->send_to );
                $provider->set_subject( $row->subject );
                $provider->set_update( $row->update );
                $sender = self::initial_sender( $registerSenders, $row->type_sender );
                if( !empty( $sender ) ) {
                    $data = unserialize( $row->data );
                    if( !empty( $data ) ) {
                        $sender->save_param($data);
                    }
                    $provider->set_sender( $sender );
                }
                $listProviders[] = $provider;
            }
        }

        return $listProviders;
    }

    public function delete( $id )
    {
        global $wpdb;

        $wpdb->delete(
            $this->table_name,
            array(
                "id" => $id
            )
        );
    }

    public function save(Scand_Multi_Mailer_Provider $provider )
    {
        global $wpdb;

        if( !empty( $provider->get_id() ) ) {
            $answer = $this->update_provider( $provider );
        } else {
            $answer = $this->insert_provider( $provider );
            $provider->set_id( $wpdb->insert_id );
        }

        return $answer;
    }

    public function drop_table()
    {
        global $wpdb;

        $sql = "DROP TABLE IF EXISTS {$this->table_name}";
        $wpdb->query( $sql );
    }

    /* @param Scand_Multi_Mailer_ISender[] $registerSenders */
    private function initial_sender($registerSenders , $typeSender)
    {
        $currentSender = null;
        if( empty( $registerSenders ) || !is_array( $registerSenders ) ) {
            $registerSenders[] = $registerSenders;
        }
        foreach( $registerSenders as $sender ) {
            if ( !empty( $sender ) && $typeSender === $sender->get_name() ) {
                $currentSender = clone $sender;
            }
        }

        return $currentSender;
    }

}