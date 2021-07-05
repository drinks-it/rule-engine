## Mark Entity Resources

| Annotation | Access | Description |
| ---------- | ------ | ----------- |
| `RuleEntityResource` | Class | Only mark entity |
| `RuleEntityProperty` | Property | Set settings for `condition`  and `action` cases

```php
<?php
use Doctrine\ORM\Mapping as ORM;
use DrinksIt\RuleEngineBundle\Mapping as RuleEngine;

/**
 * Class ModelEntity
 * @ORM\Entity(repositoryClass="ModelEntityRepositry")
 * @RuleEngine\RuleEntityResource 
 */
class ModelEntity {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     * 
     * @RuleEngine\RuleEntityProperty(
     *     condition=StringConditionAttribute::class,
     *     action=StringActionType::class
     * )
     */
    private $name;
    /**
     * @ORM\Column(type="integer")
     * 
     * @RuleEngine\RuleEntityProperty(
     *     condition=IntegerConditionAttribute::class,
     *     action=NumberActionType::class
     * )
     */
    private $price;
    /**
     * @ORM\Column(type="integer")
     * 
     * @RuleEngine\RuleEntityProperty(
     *     condition=IntegerConditionAttribute::class,
     *     action=NumberActionType::class
     * )
     */
    private $tax;
    
}
```
