<?php

namespace Drupal\rrr_http_log\Controller;

use Drupal\Core\Controller\ControllerBase;

class HttpLogController extends ControllerBase {
  
  public function build() {
    // Construct the form HTML markup
    $form_markup = '
      <form id="searchForm" action="" method="GET">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="' . (isset($_GET['start_date']) ? $_GET['start_date'] : '') . '">
        
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="' . (isset($_GET['end_date']) ? $_GET['end_date'] : '') . '">
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter email" value="' . (isset($_GET['email']) ? $_GET['email'] : '') . '">
        
        <label for="transaction_id">Transaction ID:</label>
        <input type="text" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID" value="' . (isset($_GET['transaction_id']) ? $_GET['transaction_id'] : '') . '">
        
        <label for="payment_ref">Payment Reference:</label>
        <input type="text" id="payment_ref" name="payment_ref" placeholder="Enter payment reference" value="' . (isset($_GET['payment_ref']) ? $_GET['payment_ref'] : '') . '">
        
        <button type="submit" name="submit">Search</button>
        <input type="button" name="reset" value="Reset" id="reset123" onclick="customReset();">
      </form>';
      
    // Return the render array
    // echo $form_markup;
    return [
      '#type' => "form",
		  '#markup' =>$form_markup,
		];
  }
}
