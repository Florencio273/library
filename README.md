# Library Management API 

>## Overview
>This API is built using the Slim Framework and employs JWT for authentication. It connects to a MySQL database named `library`.
>The Library Management API is a comprehensive backend solution designed to streamline the management of library resources, including books, users, and access control. This API provides a structured and efficient way to perform CRUD (Create, Read, Update, Delete) operations while maintaining security and scalability. Built with the **Slim Framework**, a lightweight yet powerful PHP framework, the API is both easy to deploy and highly customizable.
>The API is designed to meet the needs of both small-scale libraries and large institutions with extensive collections. By leveraging **RESTful principles**, it ensures that all interactions are consistent, predictable, and stateless, making it suitable for integration with web applications, mobile clients, and third-party services.

>To ensure secure communication, the Library Management API employs **JSON Web Tokens (JWT)** for authentication. This guarantees that only authorized users can access or modify resources. The authentication mechanism is designed to support role-based access control, allowing future enhancements to include permissions for different types of users (e.g., administrators, librarians, and patrons).

>The database layer of the API is powered by **MySQL**, providing a robust foundation for data storage. The database schema is optimized for efficiency and scalability, enabling fast querying even with large datasets. Features such as indexed fields and foreign key constraints ensure data integrity and performance.

>This API is built with modularity in mind. Each feature is implemented as a separate module, making it easy to add new functionalities or update existing ones. The project follows modern PHP development practices, including dependency management via **Composer** and adherence to **PSR standards** for interoperability and maintainability.

>The API's design emphasizes ease of use for developers. Endpoints are intuitive and include descriptive responses, ensuring that integration is straightforward even for developers who are new to REST APIs. Comprehensive error handling ensures that all issues are clearly communicated, making it easier to debug and resolve problems during development and production.


---



### Key Features
1. **Authentication**: 
   - Secure token-based authentication using JWT.
   - Tokens are issued upon successful login and are valid for one hour by default.

2. **User Management**:
   - Create, retrieve, and delete users.
   - Secure storage of user credentials using password hashing.

3. **Book Management**:
   - Full CRUD (Create, Read, Update, Delete) operations for managing books.
   - Support for additional metadata, such as genres and publication years.

4. **Error Handling**:
   - Clear and standardized error responses.
   - Comprehensive logging of server-side exceptions for easier debugging.

5. **Scalability**:
   - Modular codebase for easy extension.
   - Ready for integration with frontend or mobile clients.

6. **Database Integration**:
   - MySQL database for persistent storage.
   - Simplified database interaction using PDO.

### Prerequisites
1. **PHP**: Ensure PHP 7.4 or above is installed.
2. **Composer**: Used for managing dependencies.
3. **MySQL**: Set up a database named `library`.
   
---

## Routes

### Authentication
- **POST /auth/login**: Authenticate user and generate a JWT token.

### Books
- **GET /books**: Retrieve a list of all books.
- **POST /books**: Add a new book to the collection.
- **PUT /books/{id}**: Update details of an existing book.
- **DELETE /books/{id}**: Remove a book from the collection.

### Users
- **GET /users**: Retrieve a list of all users.
- **POST /users**: Add a new user.
- **DELETE /users/{id}**: Remove a user from the system.

---

## Authentication

Authentication is a cornerstone of the Library Management API, ensuring that only authorized users can interact with the system. The API employs **JSON Web Tokens (JWT)** to handle authentication securely and efficiently. JWT is an industry-standard method for securely transmitting information between parties, and it is well-suited for stateless RESTful APIs.

### How Authentication Works

1. **User Login**:
   - The user submits their credentials (username and password) to the `/auth/login` endpoint via a `POST` request.
   - The API validates the provided credentials against the database.
   - If the credentials are valid, a JWT is generated and returned to the client.

2. **Token-Based Access**:
   - For every subsequent request, the client must include the JWT in the `Authorization` header in the following format:
     ```
     Authorization: Bearer <token>
     ```
   - The API validates the token to ensure it is authentic and has not expired before granting access to the requested resource.

3. **Token Expiration**:
   - JWTs have a limited lifespan, typically 1 hour by default. This ensures that tokens cannot be reused indefinitely in case they are compromised.
   - After expiration, the user must reauthenticate by calling the `/auth/login` endpoint to obtain a new token.

4. **Statelessness**:
   - The API does not maintain session data on the server side. All user information is encoded in the token, making the system scalable and easier to manage.

---


