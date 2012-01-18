# Yii Doctrine 2 Extension

## Configuration
Set the KodeFoundry alias in the main.php config file e.g.

Yii::setPathOfAlias('KodeFoundry', realpath(dirname(__FILE__) . '/../extensions/KodeFoundry'));

Configure the component;

'doctrine' => array(
    'class' => 'KodeFoundry\Doctrine\ORM\Component',
),