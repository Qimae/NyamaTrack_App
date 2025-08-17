CREATE TABLE mpesa_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  MerchantRequestID VARCHAR(255),
  CheckoutRequestID VARCHAR(255),
  ResultCode INT,
  Amount DECIMAL(10, 2),
  MpesaReceiptNumber VARCHAR(255),
  PhoneNumber VARCHAR(255)
);