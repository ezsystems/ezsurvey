<?php
//
// Created on: <11-Jun-2004 00:00:00 Jan Kudlicka>
//
// Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
//
// This source file is part of the eZ Publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ Publish professional licences" may use this
// file in accordance with the "eZ Publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ Publish professional licence" is available at
// http://ez.no/products/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file ezsurveyoperators.php
*/

class eZSurveyOperators
{
    function eZSurveyOperators()
    {
        $this->Operators = array( 'number' );
    }

    function &operatorList()
    {
        return $this->Operators;
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'number' => array( 'is_integer' => array( 'type' => 'integer',
                                                                'required' => false,
                                                                'default' => '0' ) ) );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace,
                     $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        switch ( $operatorName )
        {
            case 'number':
            {
                $operatorValue = $this->number( $operatorValue, $namedParameters['is_integer'] );
            } break;
        }
    }

    function number( $number, $is_integer )
    {
        $locale = eZLocale::instance();

        if ( $is_integer && is_numeric( $number ) && (int) $number == $number )
        {
            $neg = $number < 0;
            $num = $neg ? -$number : $number;
            $text = number_format( $num, 0, '', $locale->thousandsSeparator() );
            $text = ( $neg ? $locale->negativeSymbol(): $locale->positiveSymbol() ) . $text;
            return $text;
        }
        else if ( is_numeric( $number ) )
            return $locale->formatNumber( $number );
        else
            return $number;
    }
}

?>
