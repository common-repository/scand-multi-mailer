<?php

/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * Scand_Multi_Mailer_Provider_Service class
 */

class Scand_Multi_Mailer_Provider_Service
{
    /* @var Scand_Multi_Mailer_Provider*/
    private $provider;
    /* @var Scand_Multi_Mailer_ISender[] */
    private $senders;

    /* @return Scand_Multi_Mailer_Provider */
    public function get_provider()
    {
        return $this->provider;
    }

    public function register_sender(Scand_Multi_Mailer_ISender $sender)
    {
        $this->senders[] = $sender;
    }

    public function set_provider(Scand_Multi_Mailer_Provider $provider)
    {
        $this->provider = $provider;
    }

    public function get_form_sender()
    {
        return ( !empty( $this->provider ) ) ? $this->provider->get_form_sender() : "";
    }

    /* @return Scand_Multi_Mailer_Provider[] */
    public function get_list_providers()
    {
        $providerRepository = new Scand_Multi_Mailer_Provider_Repository();
        $listProviders = $providerRepository->get_list_providers( $this->senders );

        return $listProviders;
    }

    public function get_senders()
    {
        return $this->senders;
    }

    public function set_sender_by_key( $key )
    {
        if ( array_key_exists($key, $this->senders) !== false && !empty( $this->provider) ) {
            $this->provider->set_sender( $this->senders[ $key ] );
        }
    }

    public function send_test_message($to, $subject, $message)
    {
        $answer = false;
        if ( ! empty($this->provider) ) {
            $answer = $this->provider->send_test_message( $to, $subject, $message);
        }

        return $answer;
    }

    public function send_message($to, $subject, $message)
    {
        $listProviders = $this->get_list_providers();
        if ( ! empty( $listProviders ) && is_array( $listProviders ) )
        {
            foreach( $listProviders as $provider )
            {
                $provider->send_message( $to, $subject, $message);
            }
        }
    }

    public function save_sender($provider_data )
    {
        if ( isset( $this->provider ) ) {
            $this->provider->set_sender_param( $provider_data );

            return $this->save();
        }
    }

    private function save()
    {
        $providerRepository = new Scand_Multi_Mailer_Provider_Repository();
        $answer = $providerRepository->save( $this->provider );

        return $answer;
    }

    public function save_mail_template($from, $from_name, $subject, $to, $message)
    {
        $this->provider->set_send_from( $from );
        $this->provider->set_send_from_name( $from_name );
        $this->provider->set_send_to( $to );
        $this->provider->set_subject( $subject );
        $this->provider->set_message( $message );

        return $this->save();
    }

    public function init_provider_by_id($id = null )
    {
        $providerRepository = new Scand_Multi_Mailer_Provider_Repository();
        $this->provider = $providerRepository->get_provider_by_id( $id, $this->senders );
    }

    public function delete_by_id($id )
    {
        $providerRepository = new Scand_Multi_Mailer_Provider_Repository();
        $providerRepository->delete( $id );
    }

    public function get_provider_id()
    {
        return ( isset( $this->provider ) ) ? $this->provider->get_id() : 0;
    }

    public function get_provider_name()
    {
        return ( isset( $this->provider ) ) ? $this->provider->get_name() : "";
    }

    public function get_sender_name()
    {
        return ( isset( $this->provider ) ) ? $this->provider->get_sender_name() : "";
    }

    public function init_table()
    {
        $providerRepository = new Scand_Multi_Mailer_Provider_Repository();
        $providerRepository->create_table();
    }

    public function set_provider_name($name )
    {
        if ( isset( $this->provider ) && !empty( $name )) {
            $this->provider->set_name( $name );

            return $this->save();
        }
    }

    public function sort_providers($orderby, $order )
    {
        $listProviders = self::get_list_providers();
        switch( $orderby ) {
            case "name":
                if( $order == 'desc' ) {
                    usort( $listProviders, ["Scand_Multi_Mailer_Provider_Service", "sort_by_name_desc"] );
                } else {
                    usort( $listProviders, ["Scand_Multi_Mailer_Provider_Service", "sort_by_name_asc"] );
                }
                break;
            case "date":
                if( $order == 'desc' ) {
                    usort( $listProviders, ["Scand_Multi_Mailer_Provider_Service", "sort_by_date_desc"] );
                } else {
                    usort( $listProviders, ["Scand_Multi_Mailer_Provider_Service", "sort_by_date_asc"] );
                }
                break;
            default:
                break;
        }

        return $listProviders;
    }

    public function uninstall()
    {
        $providerRepository = new Scand_Multi_Mailer_Provider_Repository();
        $providerRepository->drop_table();
    }

    public function sort_by_name_asc(Scand_Multi_Mailer_Provider $provider1, Scand_Multi_Mailer_Provider $provider2 )
    {
        $answer = -1;
        if ( $provider1->get_name() > $provider2->get_name() ) {
            $answer = 1;
        }

        return $answer;
    }

    public function sort_by_name_desc(Scand_Multi_Mailer_Provider $provider1, Scand_Multi_Mailer_Provider $provider2 )
    {
        $answer = -1;
        if ( $provider1->get_name() < $provider2->get_name() ) {
            $answer = 1;
        }

        return $answer;
    }

    public function sort_by_date_asc(Scand_Multi_Mailer_Provider $provider1, Scand_Multi_Mailer_Provider $provider2 )
    {
        $answer = -1;
        if ( $provider1->get_update() > $provider2->get_update() ) {
            $answer = 1;
        }

        return $answer;
    }

    public function sort_by_date_desc(Scand_Multi_Mailer_Provider $provider1, Scand_Multi_Mailer_Provider $provider2 )
    {
        $answer = -1;
        if ( $provider1->get_update() < $provider2->get_update() ) {
            $answer = 1;
        }

        return $answer;
    }
}