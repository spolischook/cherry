<?php

namespace Spolischook\GoogleCalendarBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('google_calendar');

        $rootNode
            ->children()
                ->variableNode('allowed_register_time')
                    ->info('Array of allowed register time. Key must be a day number and value is array of array {start: timeStart, end: timeEnd}')
                ->end()
                ->scalarNode('user')
                    ->info('Often it is "your_email@gmail.com"')
                ->end()
                ->scalarNode('calendar_user')
                    ->info('calendar_user and calendar_visibility: calendar dropdown -> Calendar settings -> Private Address -> “XML” link: http://www.google.com/calendar/feeds/calendar_user/calendar_visibility/basic')
                ->end()
                ->scalarNode('password')
                    ->info('Password to your google account')
                ->end()
                ->scalarNode('calendar_visibility')
                    ->info('calendar_user and calendar_visibility: calendar dropdown -> Calendar settings -> Private Address -> “XML” link: http://www.google.com/calendar/feeds/calendar_user/calendar_visibility/basic')
                ->end()
        ;
        return $treeBuilder;
    }
}
