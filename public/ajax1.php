<?php
    // Only process POST reqeusts.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Get the form fields and remove whitespace.
      $name = strip_tags(trim($_POST["name"]));
      $name = str_replace(array("\r","\n"),array(" "," "),$name);
      $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
      $phone = trim($_POST["phone"]);
      $event = trim($_POST["event"]);

      // Set the recipient email address.
      $recipient = "tennispolska@gmail.com";

      // Set the email subject.
      $subject = "$name has applied!!";

      // Build the email content.
      $email_content = "Name: $name\n";
      $email_content .= "Email: $email\n";
      $email_content .= "Phone: $phone\n";
      $email_content .= "Event: $event\n";

      // Build the email headers.
      $email_headers = "From: $name <$email>";

      // Send the email.
      if (mail($recipient, $subject, $email_content, $email_headers)) {
        // Set a 200 (okay) response code.
        http_response_code(200);
        echo "Thank You! You're all set to go!";
      } else {
        // Set a 500 (internal server error) response code.
        http_response_code(500);
        echo "Oops! Something went wrong and we get your information.";
      }
    } else {
      // Not a POST request, set a 403 (forbidden) response code.
      http_response_code(403);
      echo "There was a problem with your submission, please try again.";
    }

?>
