variable "aws_region" {
  description = "AWS region"
  type        = string
  default     = "us-west-2"
}

variable "instance_type" {
  description = "EC2 instance type"
  type        = string
  default     = "t3.medium"
}

# variable "ami_id" removed in favor of data source in main.tf

variable "key_name" {
  description = "Name of the SSH key pair to use"
  type        = string
  default     = "moodle-key"
}
