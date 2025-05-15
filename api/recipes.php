<?php
require_once '../config/database.php';
require_once '../classes/Recipe.php';

// Start session to access session variables
session_start();

// Determine if this is an API call or a form submission
$isApiCall = isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] === 'application/json';

if ($isApiCall) {
    header('Content-Type: application/json');
}

$database = new Database();
$db = $database->getConnection();

$recipe = new Recipe($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $recipes = $recipe->getAllRecipes();
        if ($isApiCall) {
            echo json_encode($recipes);
        } else {
            // Redirect for non-API calls
            header('Location: ../pages/home.php');
            exit();
        }
        break;

    case 'POST':
        // Check if this is a form submission or API call
        if (isset($_POST['title']) && isset($_POST['ingredients']) && isset($_POST['instructions'])) {
            // Form submission
            if (!isset($_SESSION['user_id'])) {
                if ($isApiCall) {
                    echo json_encode(["error" => "You must be logged in to add a recipe."]);
                } else {
                    header('Location: ../pages/login.php?redirect=add-recipe.php');
                    exit();
                }
            }

            $recipe = new Recipe(
                $db,
                $_POST['title'],
                $_POST['ingredients'],
                $_POST['instructions'],
                $_POST['category'] ?? null,
                $_SESSION['user_id']
            );
            
            // Handle additional fields
            if (isset($_POST['difficulty'])) {
                $recipe->difficulty = $_POST['difficulty'];
            }
            
            if (isset($_POST['prep_time'])) {
                $recipe->prep_time = $_POST['prep_time'];
            }
            
            if (isset($_POST['cook_time'])) {
                $recipe->cook_time = $_POST['cook_time'];
            }
            
            if (isset($_POST['servings'])) {
                $recipe->servings = $_POST['servings'];
            }
            
            // Handle notes if provided
            if (isset($_POST['notes'])) {
                $recipe->notes = $_POST['notes'];
            }
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['name'] !== '') {
                error_log("Image upload attempt: " . print_r($_FILES['image'], true));
                
                if ($_FILES['image']['error'] == 0) {
                    if (!$recipe->uploadImage($_FILES['image'])) {
                        error_log("Image upload failed: " . print_r($_FILES['image'], true));
                        // Store error in session for display
                        $_SESSION['upload_error'] = "Failed to upload image. Please try again with a different image.";
                    } else {
                        error_log("Image uploaded successfully: " . $recipe->image);
                    }
                } else {
                    // Log the upload error
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
                        UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.",
                        UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
                        UPLOAD_ERR_NO_FILE => "No file was uploaded.",
                        UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
                        UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
                        UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload."
                    ];
                    
                    $errorMessage = isset($errorMessages[$_FILES['image']['error']]) ? 
                                    $errorMessages[$_FILES['image']['error']] : 
                                    "Unknown upload error";
                    
                    error_log("Image upload error: " . $errorMessage);
                    // Store error in session for display
                    $_SESSION['upload_error'] = "Image upload error: " . $errorMessage;
                    
                    error_log("Image upload error: " . $errorMessage);
                }
            }
            
            if ($recipe->save()) {
                if ($isApiCall) {
                    echo json_encode(["message" => "Recipe created successfully."]);
                } else {
                    // Log successful save
                    error_log("Recipe saved successfully. Recipe ID: " . $recipe->id);
                    header('Location: ../pages/recipes-categories.php?success=1');
                    exit();
                }
            } else {
                if ($isApiCall) {
                    echo json_encode(["error" => "Failed to create recipe."]);
                } else {
                    // Log error for debugging
                    error_log("Failed to save recipe: " . print_r($_POST, true));
                    header('Location: ../pages/add-recipe.php?error=1');
                    exit();
                }
            }
        } else {
            // API JSON call
            $data = json_decode(file_get_contents("php://input"));
            if (!empty($data->title) && !empty($data->ingredients) && !empty($data->instructions)) {
                $recipe->title = $data->title;
                $recipe->ingredients = $data->ingredients;
                $recipe->instructions = $data->instructions;
                $recipe->category_id = $data->category_id ?? null;
                $recipe->user_id = $_SESSION['user_id'] ?? null;
                
                if ($recipe->save()) {
                    echo json_encode(["message" => "Recipe created successfully."]);
                } else {
                    echo json_encode(["error" => "Unable to create recipe."]);
                }
            } else {
                echo json_encode(["error" => "Incomplete data."]);
            }
        }
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->title) && !empty($data->ingredients) && !empty($data->instructions)) {
            // Create a new Recipe object with the data
            $recipe = new Recipe(
                $db,
                $data->title,
                $data->ingredients,
                $data->instructions,
                $data->category_id ?? null,
                $_SESSION['user_id'] ?? null
            );
            
            if ($recipe->update($data->id)) {
                echo json_encode(["message" => "Recipe updated successfully."]);
            } else {
                echo json_encode(["error" => "Unable to update recipe."]);
            }
        } else {
            echo json_encode(["error" => "Incomplete data."]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            $recipe = new Recipe($db);
            if ($recipe->delete($data->id)) {
                echo json_encode(["message" => "Recipe deleted successfully."]);
            } else {
                echo json_encode(["error" => "Unable to delete recipe."]);
            }
        } else {
            echo json_encode(["error" => "Recipe ID is required."]);
        }
        break;

    default:
        if ($isApiCall) {
            echo json_encode(["error" => "Method not allowed."]);
        } else {
            header('Location: ../pages/home.php');
            exit();
        }
        break;
}
?>
