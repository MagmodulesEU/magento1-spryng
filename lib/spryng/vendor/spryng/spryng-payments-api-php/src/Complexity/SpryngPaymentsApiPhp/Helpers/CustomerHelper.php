<?php

namespace SpryngPaymentsApiPhp\Helpers;

use SpryngPaymentsApiPhp\Exception\CustomerException;
use SpryngPaymentsApiPhp\Object\Mandate;
use SpryngPaymentsApiPhp\Object\Card;
use SpryngPaymentsApiPhp\Object\Customer;

class CustomerHelper
{

    static $SPECIAL_PARAMETERS = [
        'cards',
        'mandates'
    ];

    public static function fillCustomerObject($response)
    {
        $customer = new Customer();

        foreach($response as $key => $parameter)
        {
            if (!in_array($key, self::$SPECIAL_PARAMETERS))
            {
                $customer->$key = $parameter;
            }
            else
            {
                switch($key)
                {
                    case 'cards':
                        foreach($response->$key as $customerCard)
                        {
                            $card = new Card();
                            $card->_id = $customerCard;
                            array_push($customer->cards, $card);
                        }
                        break;
                    case 'mandates':
                        foreach($response->$key as $customerMandate)
                        {
                            $mandate = new Mandate(
                                $customerMandate->merchant,
                                $customerMandate->processor,
                                $customerMandate->reference,
                                $customerMandate->signed_at,
                                $customerMandate->status,
                                $customerMandate->_type
                            );
                            array_push($customer->mandates, $mandate);
                        }
                        break;
                }
            }
        }

        return $customer;
    }

    public static function validateNewCustomerArguments($arguments)
    {
        if (!isset($arguments['title']))
        {
            throw new CustomerException("Title is required.", 502);
        }

        if (!isset($arguments['first_name']))
        {
            throw new CustomerException("First name is required.", 502);
        }

        if (!isset($arguments['last_name']))
        {
            throw new CustomerException("Last name is required.", 502);
        }

        if (!isset($arguments['email_address']))
        {
            throw new CustomerException("Email address is required.", 502);
        }

        if (!isset($arguments['country_code']))
        {
            throw new CustomerException("Country code is required.", 502);
        }

        if (!isset($arguments['city']))
        {
            throw new CustomerException("City is required.", 502);
        }

        if (!isset($arguments['street_address']))
        {
            throw new CustomerException("Street is required.", 502);
        }

        if (!isset($arguments['postal_code']))
        {
            throw new CustomerException("Postal code is required.", 502);
        }

        if (!isset($arguments['phone_number']))
        {
            throw new CustomerException("Phone number is required.", 502);
        }
    }
}