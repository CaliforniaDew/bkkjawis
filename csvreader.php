<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Path to the CSV file
$csvFile = 'jobs.csv';

// Open the CSV file for reading
if (($handle = fopen($csvFile, 'r')) !== false) {
    // Get the first row, which contains the column headers
    $headers = fgetcsv($handle, 1000, ',');

    // Check if the required columns are present
    $requiredColumns = [
        'job_title', 'url', 'posted_date', 'job_country_code', 'is_remote', 'job_location', 
        'job_description', 'salary', 'hiring_manager_full_name', 'hiring_manager_first_name', 
        'hiring_manager_role', 'hiring_manager_linkedin_url', 'company_name', 'company_url', 
        'company_linkedin_url', 'company_industry', 'company_employee_count', 'company_revenue_usd', 
        'company_seo_description', 'company_description'
    ];
    foreach ($requiredColumns as $column) {
        if (!in_array($column, $headers)) {
            die("Missing required column: $column");
        }
    }

    // Loop through the file line-by-line
    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
        // Combine the headers with the data
        $job = array_combine($headers, $data);

        // Prepare the SQL query
        $stmt = $pdo->prepare("
            INSERT INTO jobs (
                job_title, url, posted_date, job_country_code, is_remote, job_location, 
                job_description, salary, hiring_manager_full_name, hiring_manager_first_name, 
                hiring_manager_role, hiring_manager_linkedin_url, company_name, company_url, 
                company_linkedin_url, company_industry, company_employee_count, company_revenue_usd, 
                company_seo_description, company_description
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        // Execute the query with the job data
        $stmt->execute([
            $job['job_title'],
            $job['url'],
            $job['posted_date'],
            $job['job_country_code'],
            $job['is_remote'],
            $job['job_location'],
            $job['job_description'],
            $job['salary'],
            $job['hiring_manager_full_name'],
            $job['hiring_manager_first_name'],
            $job['hiring_manager_role'],
            $job['hiring_manager_linkedin_url'],
            $job['company_name'],
            $job['company_url'],
            $job['company_linkedin_url'],
            $job['company_industry'],
            $job['company_employee_count'],
            $job['company_revenue_usd'],
            $job['company_seo_description'],
            $job['company_description']
        ]);
    }

    // Close the CSV file
    fclose($handle);

    echo "Job listings imported successfully!";
} else {
    echo "Failed to open the CSV file.";
}
?>