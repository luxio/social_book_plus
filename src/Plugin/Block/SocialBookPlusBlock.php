<?php

namespace Drupal\social_book_plus\Plugin\Block;

use Drupal\book\Plugin\Block\BookNavigationBlock;

/**
 * Provides a 'SocialBookPlusBlock' block.
 *
 * @Block(
 *  id = "social_book_plus_block",
 *  admin_label = @Translation("Social Book Plus Navigation Block"),
 *   category = @Translation("Menus")
 * )
 */
class SocialBookPlusBlock extends BookNavigationBlock {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_bid = 0;
    //ksm($this);
    //kint($this);
    if ($node = $this->requestStack->getCurrentRequest()->get('node')) {
      $current_bid = empty($node->book['bid']) ? 0 : $node->book['bid'];
    }
    if ($this->configuration['block_mode'] == 'all pages') {
      return parent::build();
    }
    elseif ($current_bid) {
      // Only display this block when the user is browsing a book and do
      // not show unpublished books.
      $nid = \Drupal::entityQuery('node')
        ->condition('nid', $node->book['bid'], '=')
        ->condition('status', NODE_PUBLISHED)
        ->execute();
      // Only show the block if the user has view access for the top-level node.
      if ($nid) {
        $tree = $this->bookManager->bookTreeAllData($node->book['bid']);
        $build = $this->bookManager->bookTreeOutput($tree);
        // Add active trail to theme
        $active_trail = $this->bookManager->getActiveTrailIds($node->book['bid'], $node->book);
        $build['#active_trail'] = $active_trail;
        // Use our heme
        $build['#theme'] = 'social_book_plus_block';
        return $build;
      }
    }
    return array();
  }

}