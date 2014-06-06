<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;

class DB {

	public static function query($resource, array $args = array(), array $options = array())
	{
		switch ($resource) {

			case 'posts':

				return self::__queryPosts($args, $options);
				break;
		}
	}

	protected static function __queryPosts(array $params = array(), array $options = array())
	{
		// Set default options
		$default_options = array(
			'with_meta' => false
		);

		// Merge default options with user input
		$options = array_merge($default_options, $options);

		// Map short references to full query argument key
		$params_map = array(
			'type'           => 'post_type',
			'status'         => 'post_status',
			'parent'         => 'post_parent',
			'mime_type'      => 'post_mime_type',
			'max_results'    => 'posts_per_page',
			'sort_by'        => 'orderby',
			'sort_direction' => 'order',
			'page'           => 'paged',
			'include'        => 'post__in',
			'exclude'        => 'post__not_in'
		);

		// Get raw parameters
		$raw_params = $params;
		
		// Defined variable for clean parameters for query
		$filtered_params = array();

		foreach ($raw_params as $key => $value) {

			// Check for tax query params
			if (preg_match('/^tax\:/', $key)) {

				$data_string = str_replace('tax:', '', $key);

				if ($data_string) {
					
					$data = null;
					
					if (Bebop::util('isJson', $data_string)) {
						
						$data = $data_string ? json_decode($data_string, true) : null;
						
					} else {

						$data = array('taxonomy' => $data_string);
					}

					if ($data) {

						if (!isset($filtered_params['tax_query'])) {
							
							$filtered_params['tax_query'] = array(

								'relation' => isset($raw_params['tax_relation']) ? $raw_params['tax_relation'] : 'AND'
							);
						}

						if (!isset($data['operator'])) $data['operator'] = 'IN';
						if (!isset($data['field'])) $data['field'] = 'slug';

						$data['terms'] = array();

						$values = explode(',', $value);

						foreach ($values as $value) {
							$data['terms'][] = $value;
						}

						$filtered_params['tax_query'][] = $data;
					}
				}

			// Check for meta query params
			} elseif (preg_match('/^meta\:/', $key)) {

				$data_string = str_replace('meta:', '', $key);

				if ($data_string) {
					
					$data = null;
					
					if (Bebop::util('isJson', $data_string)) {
						
						$data = $data_string ? json_decode($data_string, true) : null;
						
					} else {

						$data = array('key' => $data_string);
					}

					if ($data) {

						if (!isset($filtered_params['meta_query'])) {
							
							$filtered_params['meta_query'] = array(

								'relation' => isset($raw_params['meta_relation']) ? $raw_params['meta_relation'] : 'AND'
							);
						}

						if (!isset($data['compare'])) $data['compare'] = '=';
						if (!isset($data['type'])) $data['type'] = 'CHAR';
		
						$data['value'] = $value;

						$filtered_params['meta_query'][] = $data;
					}
				}

			} else {

				// Check if we should map a query parameter to a built-in query parameter
				if (array_key_exists($key, $params_map)) $key = $params_map[$key];

				// Make sure comma delimited values are converted to arrays
				// on parameters that require or admit arrays as the value
				$parameters_requiring_arrays = array(
					'author__in',
					'author__not_in',
					'category__and',
					'category__in',
					'category__not_in',
					'tag__and',
					'tag__in',
					'tag__not_in',
					'tag_slug__and',
					'tag_slug__in',
					'post_parent__in',
					'post_parent__not_in',
					'post__in',
					'post__not_in',
				);

				$parameters_admitting_arrays = array(
					'post_type',
					'post_status'
				);

				if (in_array($key, $parameters_requiring_arrays)) {
					
					$value = explode(',', $value);
				}

				if (in_array($key, $parameters_admitting_arrays)) {
					
					$value = explode(',', $value);

					if (count($value) == 1) $value = $value[0];
				}

				$filtered_params[$key] = $value;
			}
		}

		// Build new query
		$query = new \WP_Query($filtered_params);

		// If we should return meta, build response structure accordingly
		if ($options['with_meta']) {

			$meta = self::__getPaginationMeta($query, $resource_name);
			$data = array(
				'meta'  => $meta,
				'items' => $query->posts
			);
		}

		// No meta needed, just return entries
		else {

			$data = $query->posts;
		}

		// Return data
		return $data;
	}

	protected static function __getPaginationMeta(\WP_Query $query, $resource_name)
	{
		$meta  = array();
		$posts = $query->posts;

		$meta['items_total']    = (int) $query->found_posts;
		$meta['items_returned'] = (int) $query->post_count;
		$meta['total_pages']    = (int) $query->max_num_pages;
		$meta['current_page']   = (int) max(1, $query->query_vars['paged']);
		$meta['has_more']       = $meta['current_page'] == $meta['total_pages'] || $meta['total_pages'] == 0 ? false : true;

		$params = $_GET;

		// Remove post_type parameter when not querying the /posts resource
		if ($resource_name != 'posts' && isset($params['post_type'])) {

			unset($params['post_type']);
		} 

		$meta['previous_page'] = $meta['current_page'] == 1 ? null : self::__buildPreviousPageUrl($params);
		$meta['next_page']     = $meta['current_page'] == $meta['total_pages'] || $meta['total_pages'] == 0 ? null : self::__buildNextPageUrl($params);

		return $meta;
	}

	protected static function __buildPreviousPageUrl(array $params = array())
	{
		$params['page'] = isset($params['page']) ? $params['page'] - 1 : 1; 

		return '?'. self::__buildQuery($params);
	}

	protected static function __buildNextPageUrl(array $params = array())
	{
		$params['page'] = isset($params['page']) ? $params['page'] + 1 : 2;

		return '?'. self::__buildQuery($params);
	}

	protected static function __buildQuery(array $params = array())
	{
		array_walk($params, function(&$value, $key) {
			$value = urldecode($value);
		});

		return http_build_query($params);
	}
}