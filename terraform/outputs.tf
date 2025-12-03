output "test_public_ip" {
  description = "Public IP address of the Test server"
  value       = aws_eip.moodle_test_eip.public_ip
}

output "prod_public_ip" {
  description = "Public IP address of the Production server"
  value       = aws_eip.moodle_prod_eip.public_ip
}
