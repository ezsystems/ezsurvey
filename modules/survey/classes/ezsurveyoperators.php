<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
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
