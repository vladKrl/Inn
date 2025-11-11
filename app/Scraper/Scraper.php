<?php

function get_info($get_parameter): array
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://myfin.by/currency/$get_parameter");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $html = curl_exec($ch);

    $http = curl_getinfo($ch)['http_code'] ?? 0;

    curl_close($ch);

    if ($http >= 400) {
        throw new RuntimeException("HTTP code responded: $http");
    }

    if (strlen($html) > 0) {
        $dom = new DOMDocument();
        @ $dom->loadHTML($html);

        $xpath = new DOMXPath($dom);

        $course_brief_block = $xpath->query("//div[@class='course-brief-info course-brief-info--best-courses course-brief-info--desk']");

        $course_lines = $xpath->query(".//div[position() = 2][@class='course-brief-info__r']", $course_brief_block[0]);
        $return_array = [];
        $item_array = [];

        foreach ($course_lines as $course_line) {
            $course_node = $xpath->query(".//span[@class='accent']", $course_line);

            $item_array["sell"] = $course_node[0]->nodeValue; // write to json sell;
            $item_array["buy"] = $course_node[1]->nodeValue; // write to json buy

            $return_array[$get_parameter] = $item_array;
        }
    } else {
        $return_array[$get_parameter] = "Do not available on site";
    }
    return $return_array;
}

try {
    if (isset($_GET['currency']) || isset($_GET['json'])) {

        $result_array = [];

        if (isset($_GET['currency'])) {

            $get_parameter = $_GET['currency'];
            $result_array[] = get_info($get_parameter);

        } elseif (isset($_GET['json'])) {

            $data_read_file_path = "Scraper/currencies.json";
            $raw_currencies = file_get_contents($data_read_file_path);
            $currencies = json_decode($raw_currencies);

            foreach ($currencies as $currency) {
                $result_array[] = get_info(strtolower($currency));
            }
        }

        $data_write_file_path = "Scraper/result.json";
        $data_to_write = json_encode($result_array, JSON_PRETTY_PRINT);
        file_put_contents($data_write_file_path, $data_to_write);
        foreach ($result_array as $item) {
            print_r($item);
            echo    "<br>";
        }
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
 <div class="common" style="display: block; align-self: center">
     <div class="currency">
         <form action="" method="GET">
             <label for="currency"></label>
             <input type="text" name="currency" id="currency" placeholder="Type a currency">

             <button type="submit" name="currency_button" id="currency_button">Get information</button>
         </form>
     </div>
     <div class="json">
         <form action="" method="GET">
             <input type="hidden" name="json" id="json" value="json">

             <button type="submit" name="json_button" id="json_button">Get information about all available currencies</button>
         </form>
     </div>
 </div>
</body>
</html>
