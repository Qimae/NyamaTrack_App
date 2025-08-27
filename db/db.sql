-- Table for registered users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    permit VARCHAR(100) NOT NULL,
    location VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email_hash CHAR(64) NOT NULL,
    business_name_hash CHAR(64) NOT NULL,
    failed_attempts INT DEFAULT 0,
    last_failed_attempt TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user (email_hash, business_name_hash)
);
-- Table for blocked_butcheries
CREATE TABLE IF NOT EXISTS blocked_butcheries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    reason VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_business (business_name)
);
-- Table for beef transactions (daily summary)
CREATE TABLE IF NOT EXISTS beef_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(255) NOT NULL,
    transaction_date DATE NOT NULL,
    buy_price VARCHAR(255) NOT NULL,
    sell_price VARCHAR(255) NOT NULL,
    total_cash_sales VARCHAR(255) NOT NULL,
    daily_expense VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_business_date (business_name, transaction_date),
    INDEX (business_name, transaction_date)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- View for calculated fields
CREATE OR REPLACE VIEW vw_beef_transactions AS
SELECT t.*,
    -- Total Cash = Total Cash Sales + Daily Expense
    (t.total_cash_sales + t.daily_expense) AS total_cash,
    -- Total Kilos = Total Cash / Sell Price
    CASE
        WHEN t.sell_price > 0 THEN (t.total_cash_sales + t.daily_expense) / t.sell_price
        ELSE 0
    END AS total_kilos,
    -- Profit per KG = Sell Price - Buy Price
    (t.sell_price - t.buy_price) AS profit_per_kg,
    -- Profit = (Profit per KG * Total Kilos) - Daily Expense
    CASE
        WHEN t.sell_price > 0 THEN (
            (t.sell_price - t.buy_price) * (
                (t.total_cash_sales + t.daily_expense) / t.sell_price
            )
        ) - t.daily_expense
        ELSE 0
    END AS profit
FROM beef_transactions t;
-- Table for goat transactions (daily summary)
CREATE TABLE IF NOT EXISTS goat_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(255) NOT NULL,
    transaction_date DATE NOT NULL,
    buy_price VARCHAR(255) NOT NULL,
    sell_price VARCHAR(255) NOT NULL,
    total_cash_sales VARCHAR(255) NOT NULL,
    daily_expense VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_business_date (business_name, transaction_date),
    INDEX (business_name, transaction_date)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- View for calculated fields
CREATE OR REPLACE VIEW vw_goat_transactions AS
SELECT t.*,
    -- Total Cash = Total Cash Sales + Daily Expense
    (t.total_cash_sales + t.daily_expense) AS total_cash,
    -- Total Kilos = Total Cash / Sell Price
    CASE
        WHEN t.sell_price > 0 THEN (t.total_cash_sales + t.daily_expense) / t.sell_price
        ELSE 0
    END AS total_kilos,
    -- Profit per KG = Sell Price - Buy Price
    (t.sell_price - t.buy_price) AS profit_per_kg,
    -- Profit = (Profit per KG * Total Kilos) - Daily Expense
    CASE
        WHEN t.sell_price > 0 THEN (
            (t.sell_price - t.buy_price) * (
                (t.total_cash_sales + t.daily_expense) / t.sell_price
            )
        ) - t.daily_expense
        ELSE 0
    END AS profit
FROM goat_transactions t;

-- Table for mpesa transactions
CREATE TABLE mpesa_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(255) NOT NULL,
    transaction_date DATE NOT NULL,
    MerchantRequestID VARCHAR(255),
    CheckoutRequestID VARCHAR(255),
    ResultCode INT,
    Amount DECIMAL(10, 2),
    MpesaReceiptNumber VARCHAR(255),
    PhoneNumber VARCHAR(255),
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_business_date (business_name, transaction_date),
    INDEX (business_name, transaction_date)
);

-- Table for subscribers
CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    subscription_type ENUM('trial', 'paid') NOT NULL DEFAULT 'trial',
    status ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_business (business_name),
    KEY idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


