<?php
namespace Drupal\dt_request_form\Plugin\RulesAction;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a 'Delete entity' action.
 *
 * @RulesAction(
 *   id = "rules_entity_delete",
 *   label = @Translation("Delete entity"),
 *   category = @Translation("Entity"),
 *   context = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity"),
 *       description = @Translation("Specifies the entity, which should be deleted permanently.")
 *     )
 *   }
 * )
 */
class EntityDelete extends RulesActionBase {

    /**
     * Deletes the Entity.
     *
     * @param \Drupal\Core\Entity\EntityInterface $entity
     *    The entity to be deleted.
     */
    protected function doExecute(EntityInterface $entity) {
        $entity->delete();
    }

}
