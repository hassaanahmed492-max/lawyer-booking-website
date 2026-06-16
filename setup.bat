"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS lawyer_booking;"
"C:\xampp\mysql\bin\mysql.exe" -u root lawyer_booking < database\schema.sql
"C:\xampp\mysql\bin\mysql.exe" -u root lawyer_booking < database\seed.sql
