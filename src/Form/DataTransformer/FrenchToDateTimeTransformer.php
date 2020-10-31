<?php 

namespace App\Form\DataTransformer;

use DateTime;
use Symfony\Component\Form\DataTransformerInterface;

use Symfony\Component\Form\Exception\TransformationFailedException;
/**
 * 
 * S'applique sur un seul champ précis du formulaire
 */
class FrenchToDateTimeTransformer implements  DataTransformerInterface{

    //called when form field is initalized with its default data
    public function transform($datetime){

        if($datetime === null){
            return '';
        }
        $datetime->format('d/m/Y');
    }

    //called on submitted data -> transform data request in acceptable format (string->datetime)
    public function reverseTransform($frenchdate){
        if($frenchdate === null){
            //Exception
            throw new TransformationFailedException("Vous devez fournir une date !");
        }

       $date = \DateTime::createFromFormat('d/m/Y', $frenchdate);

       if($date === false){
           //Exception
           throw new TransformationFailedException("Le format de la date n'est pas le bon !");
       }
       return $date;
    }

}