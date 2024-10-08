<?php 
/* Fetch weather data from a third-party api provider and cache the result for 1 hour
*  @param string city
*  @return array
*/
function get_weather_data($cityId = 'Pori') {
    $apiKey = 'dbe0870ae9fc24382773b052cf688fcd';

    $lat = '61.485199';
    $lon = '21.797461';
    
    // Define the cache ID
    $cacheId = 'weather_data_' . $cityId;

    // Try to fetch the weather data from the cache
    if ($cache = \Drupal::cache()->get($cacheId)) {
        return $cache->data;
    }

    // Fetch the weather data from the API
    $apiUrl = 'https://api.openweathermap.org/data/3.0/onecall?lat='. $lat .'&lon='. $lon .'&exclude={part}&appid=' . $apiKey;
    try {
        $response = \Drupal::httpClient()->get($apiUrl);
        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            
            // Cache the data for 1 hour
            \Drupal::cache()->set($cacheId, $data, time() + 3600);
            return $data;
        }
    } catch (RequestException $e) {
        \Drupal::logger('weather api request or response error')
            ->error('Weather API request failed: @message', ['@message' => $e->getMessage()]);
        return [];
    }
}