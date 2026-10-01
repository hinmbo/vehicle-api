# Vehicle Management RESTful API

RESTful API για τη διαχείριση οχημάτων, αναπτυγμένο σε Vanilla PHP 8 και MySQL (PDO). Το project δημιουργήθηκε στο πλαίσιο του Backend Developer Assessment για την go creations.

---

## Tech Stack & Architecture

- **Language:** PHP 8.x (Vanilla, χωρίς εξωτερικά frameworks ή dependencies)
- **Database:** MySQL / MariaDB[^1] (μέσω PDO extension)
- **Architecture Pattern:** MVC (Routing, Controllers, Models, Validation)

---

## Project Structure

```text
vehicle-api/
├── config/
│   └── database.php         # PDO connection logic
├── public/
│   └── index.php            # Application Entry Point & Custom Router
├── src/
│   ├── Controllers/
│   │   └── VehicleController.php # HTTP Requests Handling
│   ├── Models/
│   │   └── Vehicle.php           # Database queries & dynamic SQL
│   └── Validation/
│       └── VehicleValidator.php # Input Sanitation & Validation
├── schema.sql               # Database schema & seed data
└── README.md                # Documentation

```

---

## Domain Logic & Category Grouping (`type_id`)

Στη σχεδίαση της εφαρμογής έγινε ένας ξεκάθαρος διαχωρισμός ανάμεσα στα πεδία `type_id` και `vehicle_type`:

* **`type_id` (Category / Group ID):** Αντιπροσωπεύει την εμπορική κατηγορία ή το group μεγέθους/τιμολόγησης του οχήματος. Για παράδειγμα, σε ένα σύστημα διαχείρισης στόλου:


* `type_id = 1`: Economy / Compact (π.χ. Toyota Yaris, Tesla Model 3)
* `type_id = 2`: Budget / Mini (π.χ. Fiat Panda, Peugeot 208)
* `type_id = 3`: Family / SUV (π.χ. BMW X5, Nissan Qashqai)
* `type_id = 4`: Luxury / Performance (π.χ. Porsche Taycan)
Παράλληλα, το φιλτράρισμα στη βάση με βάση ακέραιο δείκτη (`type_id`) προσφέρει πολύ καλύτερα performance αποτελέσματα.




* **`vehicle_type`:** Αφορά τον τύπο του αμαξώματος (Body Style), όπως `hatchback`, `sedan`, `suv` ή `sports`.



---

## Assumptions & Testing Environment

1. **Vanilla PHP Approach:** Επιλέχθηκε η ανάπτυξη σε καθαρή PHP χωρίς frameworks (Laravel/Symfony) για την επίδειξη βασικών αρχών OOP, custom HTTP routing και ασφαλούς χρήσης του PDO.


2. **Cross-Platform Tested:** Η εφαρμογή αναπτύχθηκε σε περιβάλλον **Windows 11** (με Native PHP CLI & MySQL Server) και στη συνέχεια **δοκιμάστηκε επιτυχώς και σε Linux environment (με MariaDB)**. Το API είναι 100% cross-platform και έτοιμο για deploy σε οποιοδήποτε OS.
3. **Database Security:** Όλα τα queries (ακόμα και τα δυναμικά φίλτρα και τα sorts) εκτελούνται αποκλειστικά μέσω PDO Prepared Statements.


4. **Validation Rules:**
* Τα πεδία `model_name` και `type_id` είναι υποχρεωτικά.


* Το `price` απαιτεί αριθμό >= 0, ενώ τα `doors` ακέραιο > 0.


* Τα πεδία `transmission` και `fuel` ελέγχονται αυστηρά με βάση τα ορισμένα ENUMs (`manual`/`automatic` και `petrol`/`diesel`/`hybrid`/`electric`).





---

## Key Priorities

1. **Separation of Concerns:** Καθαρός διαχωρισμός ευθυνών μεταξύ Routing, Controller business logic, Model SQL queries και Validation.


2. **SQL Injection Prevention:** Πλήρης χρήση prepared statements και strict Parameter Binding.


3. **Combined Filtering & Sorting:** Δυνατότητα ταυτόχρονης εφαρμογής πολλαπλών φίλτρων (`price_min`, `price_max`, `transmission`, `type_id`) μαζί με ταξινόμηση.


4. **Proper HTTP Status Codes:** Επιστροφή κατάλληλων status codes (`200`, `201`, `400`, `404`, `405`, `500`) με δομημένα JSON error responses.



---

## How to Run

### 1. Database Setup

Ενημερώστε το `config/database.php` με τα στοιχεία της τοπικής σας βάσης δεδομένων (MySQL ή MariaDB):

```php
private string $host = "127.0.0.1";
private string $db_name = "vehicle_db";
private string $username = "root";
private string $password = "y0ur_p455w0rd";

```

### 2. Initialize Database (`schema.sql`)

**Linux / macOS / CMD:**

```bash
mysql -u root -p < schema.sql
```

**PowerShell (Windows):**

```powershell
Get-Content schema.sql | mysql -u root -p
```

*(ή δίνοντας το πλήρες path του mysql.exe **αν** δεν είναι ορισμένο στο PATH)*

### 3. Start Built-in PHP Server

Από τη root directory του project:

```bash
php -S localhost:8000 -t public
```

Το API είναι προσβάσιμο στο `http://localhost:8000/vehicles`.

---

## Endpoints & Usage Examples

### 1. GET /vehicles (Filtering & Sorting)



* **Combined Filter:** `GET /vehicles?type_id=1&transmission=automatic&price_min=100&sort=price_asc`

* **Sorting:** `GET /vehicles?sort=name_asc` | `GET /vehicles?sort=price_desc`

* **Price Range:** `GET /vehicles?price_min=100&price_max=200`


**Response (200 OK):**

```json
[
    {
        "id": 1,
        "model_name": "Fiat Panda",
        "type_id": 2,
        "vehicle_type": "hatchback",
        "doors": 4,
        "transmission": "manual",
        "fuel": "petrol",
        "price": 90.00
    }
]
```

### 2. POST /vehicles



* **Headers:** `Content-Type: application/json`
* **Body:**

```json
{
    "model_name": "Toyota Yaris",
    "type_id": 1,
    "vehicle_type": "hatchback",
    "doors": 5,
    "transmission": "automatic",
    "fuel": "hybrid",
    "price": 150.00
}
```

**Response (201 Created):**

```json
{
    "id": 11,
    "model_name": "Toyota Yaris",
    "type_id": 1,
    "vehicle_type": "hatchback",
    "doors": 5,
    "transmission": "automatic",
    "fuel": "hybrid",
    "price": 150.00
}
```

### 3. PUT /vehicles/{id}



* **Headers:** `Content-Type: application/json`
* **Body:**

```json
{
    "model_name": "Toyota Yaris Hybrid GR",
    "type_id": 1,
    "vehicle_type": "hatchback",
    "doors": 5,
    "transmission": "automatic",
    "fuel": "hybrid",
    "price": 175.00
}
```

**Response (200 OK):**

```json
{
    "id": 11,
    "model_name": "Toyota Yaris Hybrid GR",
    "type_id": 1,
    "vehicle_type": "hatchback",
    "doors": 5,
    "transmission": "automatic",
    "fuel": "hybrid",
    "price": 175.00
}
```

### 4. DELETE /vehicles/{id}



**cURL / Terminal:**

```bash
curl -X DELETE http://localhost:8000/vehicles/11
```

**PowerShell:**

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/vehicles/11" -Method Delete
```

**Response (200 OK):**

```json
{
    "message": "Vehicle deleted successfully."
}
```

[^1]: Η χρήση του πακέτου MariaDB έγινε στο πλαίσιο testing στο περιβάλλον τον Linux
