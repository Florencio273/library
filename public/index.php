<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require '../src/vendor/autoload.php';
$app = new \Slim\App;
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lib";

$app->add(function (Request $request, Response $response, $next) {
    $response = $response->withHeader('Access-Control-Allow-Origin', '*')  
                        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

    if ($request->getMethod() == 'OPTIONS') {
        return $response->withStatus(200);
    }

    return $next($request, $response);
});

// Function to generate a new access token
function generateAccessToken() {
    $key = 'server_hack';
    $iat = time();
    $accessExp = $iat + 3600;
    $payload = [
        'iss' => 'http://library.org',
        'aud' => 'http://library.com',
        'iat' => $iat,
        'exp' => $accessExp,
    ];
    return JWT::encode($payload, $key, 'HS256');
}

// Function to store tokens in the database
function storeToken($token) {
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    $sql = "INSERT INTO jwt_tokens (token, used, created_at) VALUES (:token, 0, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':token' => $token]);
}

// Function to delete expired tokens
function deleteExpiredTokens() {
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    $sql = "DELETE FROM jwt_tokens WHERE created_at < NOW() - INTERVAL 1 DAY";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
}

// Validate token function
function validateToken($request, $response, $next) {
    deleteExpiredTokens();
    $data = json_decode($request->getBody(), true);

    if (!isset($data['token'])) {
        return $response->withStatus(401)->write(json_encode(["status" => "fail", "access_token" => null, "message" => "Token missing"]));
    }

    $token = $data['token'];
    $key = 'server_hack';

    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));

        if ($decoded->exp < time()) {
            return $response->withStatus(401)->write(json_encode(["status" => "fail", "access_token" => null, "message" => "Token expired"]));
        }
        
        $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
        $sql = "SELECT used FROM jwt_tokens WHERE token = :token";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':token' => $token]);
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tokenData || $tokenData['used'] == 1) {
            return $response->withStatus(401)->write(json_encode(["status" => "fail", "access_token" => null, "message" => "Token already used or invalid"]));
        }
        

    } catch (Exception $e) {
        return $response->withStatus(401)->write(json_encode(["status" => "fail", "access_token" => null, "message" => "Unauthorized"]));
    }

    return $next($request, $response);
}

// Function to mark the token as used
function markTokenAsUsed($token) {
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    $sql = "UPDATE jwt_tokens SET used = 1 WHERE token = :token";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':token' => $token]);
}

// Function to respond with new access token 
function respondWithNewAccessToken(Response $response) {
    $newAccessToken = generateAccessToken();
    storeToken($newAccessToken);
    return $response->withHeader('New-Access-Token', $newAccessToken);
}

// Register a new user
$app->post('/user/register', function (Request $request, Response $response) use ($servername, $username, $password, $dbname) {
    $data = json_decode($request->getBody());
    $uname = $data->username;
    $pass = $data->password;
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (username, password) VALUES (:uname, :pass)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':uname' => $uname, ':pass' => $hashedPassword]);
        $response->getBody()->write(json_encode(["status" => "success", "access_token" => null, "data" => null]));

    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "access_token" => null, "data" => ["title" => $e->getMessage()]]));
    }

    $conn = null;
    return $response;
});

// Authenticate a user and generate access token
$app->post('/user/auth', function (Request $request, Response $response, array $args) use ($servername, $username, $password, $dbname) {
    $data = json_decode($request->getBody());
    $uname = $data->username;
    $pass = $data->password;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $sql = "SELECT * FROM users WHERE username = :uname";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':uname' => $uname]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Log fetched user data
        error_log('User data from DB: ' . print_r($userData, true));

        if ($userData) {
            // Check if password matches
            if (password_verify($pass, $userData['password'])) {

                // Authentication successful
                $accessToken = generateAccessToken(); 
                storeToken($accessToken); 

                // Return the generated access token in the response
                $response->getBody()->write(json_encode([
                    "status" => "success",
                    "access_token" => $accessToken, // Return the token
                    "data" => null
                ]));
            } else {
                // Password verification failed
                $response->getBody()->write(json_encode([
                    "status" => "fail", 
                    "access_token" => null, 
                    "data" => ["title" => "Authentication Failed"]
                ]));
            }
        } else {
            // User not found in the database
            $response->getBody()->write(json_encode([
                "status" => "fail", 
                "access_token" => null, 
                "data" => ["title" => "User Not Found"]
            ]));
        }
    } catch (PDOException $e) {
        // Database error
        $response->getBody()->write(json_encode([
            "status" => "fail", 
            "access_token" => null, 
            "data" => ["title" => $e->getMessage()]
        ]));
    }
    $conn = null;
    return $response;
});


// Create Author
$app->post('/authors', function (Request $request, Response $response) {
    $data = json_decode($request->getBody(), true);
    $name = $data['name'];

    // Database connection
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    $sql = "INSERT INTO authors (name) VALUES (:name)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':name' => $name]);

    // Generate and store a new access token (optional if you want a new token on each action)
    $response = respondWithNewAccessToken($response); 

    // Get the new token from the response header
    $newAccessToken = $response->getHeader('New-Access-Token')[0];

    // Write the success response including the new access token
    $response->getBody()->write(json_encode([
        "status" => "success",
        "access_token" => $newAccessToken,  // Return the new token in the response body as well
        "data" => null
    ]));

    // Return the response with the new access token in the header
    return $response;
})->add('validateToken');


// View Authors
$app->get('/authors/get', function (Request $request, Response $response) {
    // Fetch query parameters for pagination
    $params = $request->getQueryParams();
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 24; // Default to 24 if not provided

    // Calculate offset based on page and page_size
    $offset = ($page - 1) * $pageSize;
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");

    // SQL query to fetch authors with LIMIT and OFFSET
    $sql = "SELECT * FROM authors ORDER BY authorid LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch authors
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the response with authors data
    $response->getBody()->write(json_encode([
        "status" => "success",
        "data" => $authors
    ]));

    return $response;
});


// Update Author
$app->put('/authors/update/{id}', function (Request $request, Response $response, array $args) {
    // Parse the incoming JSON data from the request body
    $data = json_decode($request->getBody(), true);
    
    // Get the ID from the URL and the author name from the request body
    $id = $args['id'];
    $name = $data['name'];
    
    // Database connection
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    
    // Prepare the SQL query to update the author's name
    $sql = "UPDATE authors SET name = :name WHERE authorid = :id";
    $stmt = $conn->prepare($sql);
    
    // Execute the update query
    $stmt->execute([':name' => $name, ':id' => $id]);

    // Invalidate the current token and generate a new access token
    markTokenAsUsed($data['token']);
    $response = respondWithNewAccessToken($response);
    
    // Write the success response including the new access token
    $response->getBody()->write(json_encode([
        "status" => "success",
        "access_token" => $response->getHeader('New-Access-Token')[0],
        "data" => null
    ]));

    // Return the response with the new access token
    return $response;
})->add('validateToken');


// Delete Author
$app->delete('/authors/delete/{id}', function (Request $request, Response $response, array $args) {
    // Get the author ID to be deleted from the URL
    $id = $args['id'];

    // Database connection
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");

    // Get the token from the body or header
    $data = $request->getParsedBody();
    $token = $data['token'] ?? null;

    if ($token) {
        // Mark the current token as invalidated in your database
        markTokenAsUsed($token);
    }

    // Prepare the SQL query to delete the author
    $sql = "DELETE FROM authors WHERE authorid = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);

    // Generate a new access token for the next request
    $response = respondWithNewAccessToken($response);

    // Get the new access token from the response header
    $newAccessToken = $response->getHeader('New-Access-Token')[0];

    // Write the success response including the new access token
    $response->getBody()->write(json_encode([
        "status" => "success",
        "access_token" => $newAccessToken,  // Return the new token in the response body
        "data" => null
    ]));

    // Return the response with the new access token in the header
    return $response;
})->add('validateToken');

// Fetch  Authors Count
$app->get('/authors/count', function (Request $request, Response $response) {
    try {
        // Establish database connection
        $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query to count total authors in the authors table
        $sql = "SELECT COUNT(*) AS total_authors FROM authors";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Fetch and process the result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalAuthorsCount = $row ? $row['total_authors'] : 0;

        // Respond with count
        $response->getBody()->write(json_encode([
            "status" => "success",
            "data" => [
                "total_authors_count" => $totalAuthorsCount
            ]
        ]));
    } catch (PDOException $e) {
        // Handle errors
        $response->getBody()->write(json_encode([
            "status" => "fail",
            "message" => $e->getMessage()
        ]));
    }

    return $response->withHeader('Content-Type', 'application/json');
});

// Authors Search Endpoint
$app->post('/authors/search', function (Request $request, Response $response) {
    // Get pagination parameters from query params
    $params = $request->getQueryParams();
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 24;
    $offset = ($page - 1) * $pageSize;

    // Parse the request body for token and searchTerm
    $data = json_decode($request->getBody(), true);
    $token = $data['token'] ?? null;
    $searchTerm = $data['searchTerm'] ?? '';

    // Validate the search term
    if (empty($searchTerm)) {
        $response->getBody()->write(json_encode([
            "status" => "error",
            "message" => "Please provide a search query."
        ]));
        return $response->withStatus(400);
    }

    // Database connection
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");

    // Modify the query to search across the authorid and name fields
    $sql = "SELECT * FROM authors 
            WHERE authorid LIKE :searchTerm OR name LIKE :searchTerm
            ORDER BY name
            LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark the current token as used so it can't be reused
    if ($token) {
        markTokenAsUsed($token);
    }

    // Generate and store a new access token for the next request
    $response = respondWithNewAccessToken($response);
    $newAccessToken = $response->getHeader('New-Access-Token')[0];

    // Return the filtered list of authors with a new access token
    $response->getBody()->write(json_encode([
        "status" => "success",
        "access_token" => $newAccessToken,
        "data" => $authors
    ]));

    return $response;
})->add('validateToken');

// Filter Authors by Alphabet
$app->post('/authors/filter/{letter}', function (Request $request, Response $response, array $args) {
    $letter = $args['letter'];
    
    // Validate if the letter is a valid alphabet character
    if (!preg_match('/^[a-zA-Z]$/', $letter)) {
        $response->getBody()->write(json_encode([
            "status" => "error",
            "message" => "Invalid letter."
        ]));
        return $response->withStatus(400);
    }

    // Pagination parameters
    $params = $request->getQueryParams();
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 24; // default to 24 if not provided
    $offset = ($page - 1) * $pageSize;

    // Retrieve token from body if needed and validate as per your logic
    $data = json_decode($request->getBody(), true);
    $token = $data['token'] ?? null;

    // Database connection
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");

    // Use LIMIT and OFFSET in the query for pagination
    $sql = "SELECT * FROM authors WHERE name LIKE :letter ORDER BY name LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':letter', $letter . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark the current token as used and respond with new token if that’s your logic
    if ($token) {
        markTokenAsUsed($token);
    }
    $response = respondWithNewAccessToken($response);
    $newAccessToken = $response->getHeader('New-Access-Token')[0];

    // Return the response with the updated access token and authors data
    $response->getBody()->write(json_encode([
        "status" => "success",
        "access_token" => $newAccessToken,
        "data" => $authors
    ]));

    return $response;
})->add('validateToken');

// Create Book
$app->post('/books', function (Request $request, Response $response) {
    // Get the data from the request body
    $data = json_decode($request->getBody(), true);
    $title = $data['title'];
    $author_id = $data['author_id'];

    // Database connection
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    $sql = "INSERT INTO books (title, authorid) VALUES (:title, :authorid)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':title' => $title, ':authorid' => $author_id]);

    // Generate and store a new access token (optional if you want a new token on each action)
    $response = respondWithNewAccessToken($response); 

    // Get the new token from the response header
    $newAccessToken = $response->getHeader('New-Access-Token')[0];

    // Write the success response including the new access token
    $response->getBody()->write(json_encode([
        "status" => "success",
        "access_token" => $newAccessToken,  // Return the new token in the response body as well
        "data" => null
    ]));

    // Return the response with the new access token in the header
    return $response;
})->add('validateToken');

// Fetch  Books Count
$app->get('/books/count', function (Request $request, Response $response) {
    try {
        // Establish database connection
        $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query to count total books in the books table
        $sql = "SELECT COUNT(*) AS total_books FROM books";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Fetch and process the result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalBooksCount = $row ? $row['total_books'] : 0;

        // Respond with count
        $response->getBody()->write(json_encode([
            "status" => "success",
            "data" => [
                "total_books_count" => $totalBooksCount
            ]
        ]));
    } catch (PDOException $e) {
        // Handle errors
        $response->getBody()->write(json_encode([
            "status" => "fail",
            "message" => $e->getMessage()
        ]));
    }

    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/books/get', function (Request $request, Response $response) {
    $params = $request->getQueryParams();
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 24; // Default to 24 if not provided

    // Calculate offset based on page and page_size
    $offset = ($page - 1) * $pageSize;

    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    // Apply LIMIT and OFFSET to the query
    $sql = "SELECT * FROM books ORDER BY bookid LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode([
        "status" => "success",
        "data" => $books
    ]));

    return $response;
});

// Update Book
$app->put('/books/update/{id}', function (Request $request, Response $response, array $args) {
    $data = json_decode($request->getBody(), true);
    $id = $args['id'];
    $title = $data['title'];
    $author_id = $data['author_id'];

    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    $sql = "UPDATE books SET title = :title, authorid = :authorid WHERE bookid = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':title' => $title, ':authorid' => $author_id, ':id' => $id]);

    markTokenAsUsed($data['token']);
    $response = respondWithNewAccessToken($response);
    $response->getBody()->write(json_encode(["status" => "success", "access_token" => $response->getHeader('New-Access-Token')[0], "data" => null]));
    return $response;
})->add('validateToken');

// Delete Book
$app->delete('/books/delete/{id}', function (Request $request, Response $response, array $args) {
    // Get the book ID to be deleted from the URL
    $id = $args['id'];

    // Database connection
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");

    // Get the token from the body or header
    $data = $request->getParsedBody();
    $token = $data['token'] ?? null;

    if ($token) {
        // Mark the current token as invalidated in your database
        markTokenAsUsed($token);
    }

    // Prepare the SQL query to delete the book
    $sql = "DELETE FROM books WHERE bookid = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);

    // Generate a new access token for the next request
    $response = respondWithNewAccessToken($response);

    // Get the new access token from the response header
    $newAccessToken = $response->getHeader('New-Access-Token')[0];

    // Write the success response including the new access token
    $response->getBody()->write(json_encode([
        "status" => "success",
        "access_token" => $newAccessToken,  // Return the new token in the response body
        "data" => null
    ]));

    // Return the response with the new access token in the header
    return $response;
})->add('validateToken');


// Filter Books by Alphabet
$app->post('/books/filter/{letter}', function (Request $request, Response $response, array $args) {
    $letter = $args['letter'];
    if (!preg_match('/^[a-zA-Z]$/', $letter)) {
        $response->getBody()->write(json_encode([
            "status" => "error",
            "message" => "Invalid letter."
        ]));
        return $response->withStatus(400);
    }

    $params = $request->getQueryParams();
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 24; // default to 24 if not provided
    $offset = ($page - 1) * $pageSize;

    // Retrieve token from body if needed and validate as per your logic
    $data = json_decode($request->getBody(), true);
    $token = $data['token'] ?? null;

    // Database connection
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    // Use LIMIT and OFFSET in the query
    $sql = "SELECT * FROM books WHERE title LIKE :letter ORDER BY title LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':letter', $letter . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark the current token as used and respond with new token if that’s your logic
    if ($token) {
        markTokenAsUsed($token);
    }
    $response = respondWithNewAccessToken($response);
    $newAccessToken = $response->getHeader('New-Access-Token')[0];

    $response->getBody()->write(json_encode([
        "status" => "success",
        "access_token" => $newAccessToken,
        "data" => $books
    ]));

    return $response;
})->add('validateToken');


$app->post('/books/search', function (Request $request, Response $response) {
    // Get pagination parameters from query params
    $params = $request->getQueryParams();
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 24;
    $offset = ($page - 1) * $pageSize;

    // Parse the request body for token and searchTerm
    $data = json_decode($request->getBody(), true);
    $token = $data['token'] ?? null;
    $searchTerm = $data['searchTerm'] ?? '';

    // Validate the search term
    if (empty($searchTerm)) {
        $response->getBody()->write(json_encode([
            "status" => "error",
            "message" => "Please provide a search query."
        ]));
        return $response->withStatus(400);
    }

    // Database connection
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");

    // Modify the query to search across multiple fields if desired.
    // For example, search in both title and authorid:
    $sql = "SELECT * FROM books 
            WHERE title LIKE :searchTerm OR authorid LIKE :searchTerm
            ORDER BY title
            LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark the current token as used so it can't be reused
    if ($token) {
        markTokenAsUsed($token);
    }

    // Generate and store a new access token for the next request
    $response = respondWithNewAccessToken($response);
    $newAccessToken = $response->getHeader('New-Access-Token')[0];

    // Return the filtered list of books with a new access token
    $response->getBody()->write(json_encode([
        "status" => "success",
        "access_token" => $newAccessToken,
        "data" => $books
    ]));

    return $response;
})->add('validateToken');



// Get Books by Author ID
$app->post('/books/get_by_author', function (Request $request, Response $response) {
    $data = json_decode($request->getBody(), true);
    $author_id = $data['author_id'];

    if (empty($author_id)) {
        return $response->withStatus(400)->write(json_encode(["status" => "fail", "access_token" => null, "message" => "author_id is required"]));
    }

    try {
        $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
        $sql = "SELECT * FROM books WHERE authorid = :authorid";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':authorid' => $author_id]);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        markTokenAsUsed($request->getParsedBody()['token']);
        $response = respondWithNewAccessToken($response);
        $response->getBody()->write(json_encode(["status" => "success", "access_token" => $response->getHeader('New-Access-Token')[0], "data" => $books]));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "access_token" => null, "data" => ["title" => $e->getMessage()]]));
    }

    return $response;
})->add('validateToken');

//Borrow a Book
$app->post('/borrow', function (Request $request, Response $response) use ($servername, $username, $password, $dbname) {
    $data = json_decode($request->getBody(), true);
    $borrower_name = $data['borrower_name'];
    $book_id = $data['book_id'];
    $borrow_date = $data['borrow_date'];
    $due_date = $data['due_date'];

    try {
        // Database connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the book is available for borrowing
        $bookStmt = $conn->prepare("SELECT * FROM books WHERE bookid = :book_id AND is_borrowed = 0");
        $bookStmt->execute([':book_id' => $book_id]);
        $book = $bookStmt->fetch(PDO::FETCH_ASSOC);

        if ($book) {
            // Insert the borrow record
            $borrowStmt = $conn->prepare("INSERT INTO borrowed_books (borrower_name, book_id, borrow_date, due_date) VALUES (:borrower_name, :book_id, :borrow_date, :due_date)");
            $borrowStmt->execute([
                ':borrower_name' => $borrower_name,
                ':book_id' => $book_id,
                ':borrow_date' => $borrow_date,
                ':due_date' => $due_date
            ]);

            // Mark the book as borrowed
            $updateStmt = $conn->prepare("UPDATE books SET is_borrowed = 1 WHERE bookid = :book_id");
            $updateStmt->execute([':book_id' => $book_id]);

            // Do not mark the token as used here, unless explicitly necessary (e.g., logout or token expiration)
            $response = respondWithNewAccessToken($response); // Ensure new token is always returned

            $response->getBody()->write(json_encode([
                "status" => "success",
                "message" => "Book borrowed successfully",
                "access_token" => $response->getHeader('New-Access-Token')[0] // Return the new access token
            ]));
        } else {
            // If book is unavailable
            $response = respondWithNewAccessToken($response); // Return a new token even in failure

            $response->getBody()->write(json_encode([
                "status" => "fail",
                "message" => "Book is currently unavailable",
                "access_token" => $response->getHeader('New-Access-Token')[0]
            ]));
        }
    } catch (PDOException $e) {
        $response = respondWithNewAccessToken($response); // Regenerate token on error

        $response->getBody()->write(json_encode([
            "status" => "fail",
            "message" => "Error borrowing book: " . $e->getMessage(),
            "access_token" => $response->getHeader('New-Access-Token')[0] // Ensure this header is returned
        ]));
    }

    return $response;
})->add('validateToken');


//Return Book
$app->post('/return', function (Request $request, Response $response) use ($servername, $username, $password, $dbname) {
    $data = json_decode($request->getBody(), true);
    $book_id = $data['book_id'];
    $borrower_name = $data['borrower_name'];
    $return_date = $data['return_date'];
    $token = $data['token']; // Get the token from the request payload

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the borrowing record exists
        $borrowStmt = $conn->prepare("SELECT * FROM borrowed_books WHERE book_id = :book_id AND borrower_name = :borrower_name AND return_date IS NULL");
        $borrowStmt->execute([ ':book_id' => $book_id, ':borrower_name' => $borrower_name ]);
        $borrowRecord = $borrowStmt->fetch(PDO::FETCH_ASSOC);

        // Generate and send the new access token before invalidating the old one
        $response = respondWithNewAccessToken($response);

        if ($borrowRecord) {
            // Update the return date for the borrowed record
            $updateBorrowStmt = $conn->prepare("UPDATE borrowed_books SET return_date = :return_date WHERE id = :id");
            $updateBorrowStmt->execute([ ':return_date' => $return_date, ':id' => $borrowRecord['id'] ]);

            // Update the book availability status
            $updateBookStmt = $conn->prepare("UPDATE books SET is_borrowed = 0 WHERE bookid = :book_id");
            $updateBookStmt->execute([ ':book_id' => $book_id ]);

            // Send the success response with the new token
            $response->getBody()->write(json_encode([
                "status" => "success",
                "message" => "Book returned successfully",
                "access_token" => $response->getHeader('New-Access-Token')[0] // Return the new token
            ]));
        } else {
            // If no borrow record found, send failure response with the new token
            $response->getBody()->write(json_encode([
                "status" => "fail",
                "message" => "No active borrowing record found for this book and borrower",
                "access_token" => $response->getHeader('New-Access-Token')[0] // Return the new token
            ]));
        }

        // Mark the old token as used (after sending the new one)
        markTokenAsUsed($token);

    } catch (PDOException $e) {
        // In case of any error, send error message with the new token
        $response->getBody()->write(json_encode([
            "status" => "fail",
            "message" => $e->getMessage(),
            "access_token" => $response->getHeader('New-Access-Token')[0] // Return the new token
        ]));
    }

    return $response;
})->add('validateToken');


// List All Currently Borrowed Books
$app->get('/books/borrowed', function (Request $request, Response $response) use ($servername, $username, $password, $dbname) {
    $params = $request->getQueryParams();
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 24; // Default to 24 if not provided

    // Calculate offset based on page and page_size
    $offset = ($page - 1) * $pageSize;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Apply LIMIT and OFFSET to the query
        $stmt = $conn->prepare("
            SELECT b.bookid, b.title, bb.borrower_name, bb.borrow_date, bb.due_date
            FROM books b
            JOIN borrowed_books bb ON b.bookid = bb.book_id
            WHERE bb.return_date IS NULL
            ORDER BY bb.borrow_date DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $borrowedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the response
        $response->getBody()->write(json_encode([
            "status" => "success",
            "data" => $borrowedBooks
        ]));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode([
            "status" => "fail",
            "message" => $e->getMessage()
        ]));
    }

    return $response;
});

// Create Book-Author Relations
$app->post('/books_authors', function (Request $request, Response $response) {
    $data = json_decode($request->getBody(), true);
    $book_id = $data['book_id'];
    $author_id = $data['author_id'];

    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    $sql = "INSERT INTO books_authors (bookid, authorid) VALUES (:bookid, :authorid)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':bookid' => $book_id, ':authorid' => $author_id]);

    markTokenAsUsed($data['token']);
    $response = respondWithNewAccessToken($response);
    $response->getBody()->write(json_encode(["status" => "success", "access_token" => $response->getHeader('New-Access-Token')[0], "data" => null]));
    return $response;
})->add('validateToken');

// Get All Book-Author Relations
$app->get('/books_authors/get', function (Request $request, Response $response) {
    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    $stmt = $conn->query("SELECT * FROM books_authors");
    $relations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    markTokenAsUsed($request->getParsedBody()['token']);
    $response = respondWithNewAccessToken($response);
    $response->getBody()->write(json_encode(["status" => "success", "access_token" => $response->getHeader('New-Access-Token')[0], "data" => $relations]));
    return $response;
})->add('validateToken');

// Delete Book-Author Relations
$app->delete('/books_authors/delete/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];

    $conn = new PDO("mysql:host=localhost;dbname=library", "root", "");
    $sql = "DELETE FROM books_authors WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);

    markTokenAsUsed($request->getParsedBody()['token']);
    $response = respondWithNewAccessToken($response);
    $response->getBody()->write(json_encode(["status" => "success", "access_token" => $response->getHeader('New-Access-Token')[0], "data" => null]));
    return $response;
})->add('validateToken');

$app->run();
