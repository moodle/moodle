resource "azurerm_resource_group" "learningHubMoodleResourceGroup" {
    name        = var.ResourceGroupName
    location    = var.ResourceGroupLocation
}

resource "azurerm_storage_account" "storage_account" {
  name                     = var.StorageAccountName
  resource_group_name      = azurerm_resource_group.learningHubMoodleResourceGroup.name
  location                 = azurerm_resource_group.learningHubMoodleResourceGroup.location
  account_tier             = "Standard"
  account_replication_type = "LRS"
}

resource "azurerm_storage_share" "storage_share" {
  name                 = "moodledata"
  storage_account_name = azurerm_storage_account.storage_account.name
  quota                = var.StorageQuota
}

resource "azurerm_storage_share" "storage_share_theme" {
  name                 = "moodletheme"
  storage_account_name = azurerm_storage_account.storage_account.name
  quota                = var.StorageQuota
}

resource "azurerm_storage_container" "assessment_container" {
  name                  = "assessmentstoragecontainer"
  storage_account_name  = azurerm_storage_account.storage_account.name
  container_access_type = "private"
}

resource "azurerm_kubernetes_cluster" "aks" {
  name                = var.ClusterName
  location            = azurerm_resource_group.learningHubMoodleResourceGroup.location
  resource_group_name = azurerm_resource_group.learningHubMoodleResourceGroup.name
  dns_prefix          = var.ClusterName
  default_node_pool {
    name                         = "default"
    vm_size                      = "Standard_B4ms"
    temporary_name_for_rotation  = "tmpnodepool1"
	auto_scaling_enabled         = true
    min_count                    = 2
    max_count                    = 3
    only_critical_addons_enabled = true
  }
  identity {
    type = "SystemAssigned"
  }
  network_profile {
    network_plugin = "azure"
  }
  api_server_authorized_ip_ranges = [
    "86.28.98.0/24"
  ]
  tags = {
    environment = var.Environment
  }
}

resource "azurerm_kubernetes_cluster_node_pool" "user_node_pool" {
  name                  = "userpool"
  kubernetes_cluster_id = azurerm_kubernetes_cluster.aks.id
  vm_size               = var.ClusterNodeSize
  node_count            = var.ClusterNodeCount
  mode                  = "User"
  tags = {
    Environment = var.Environment
  }
}

resource "azurerm_container_registry" "containerRegistry" {
  name                = var.ContainerRegistryName
  resource_group_name = azurerm_resource_group.learningHubMoodleResourceGroup.name
  location            = azurerm_resource_group.learningHubMoodleResourceGroup.location
  sku                 = "Basic"
  admin_enabled       = true
}

resource "azurerm_virtual_network" "vnet" {
  name                = "ManagedInstanceVnet"
  address_space       = ["10.0.0.0/16"]
  location            = azurerm_resource_group.learningHubMoodleResourceGroup.location
  resource_group_name = azurerm_resource_group.learningHubMoodleResourceGroup.name
}

resource "azurerm_network_security_group" "nsg" {
  name                = "ManagedInstanceNSG"
  location            = azurerm_resource_group.learningHubMoodleResourceGroup.location
  resource_group_name = azurerm_resource_group.learningHubMoodleResourceGroup.name
  security_rule {
    name                       = "AllowInbound"
    description                = "Allow inbound traffic"
    direction                  = "Inbound"
    access                     = "Allow"
    priority                   = 100
    protocol                   = "Tcp"
    source_port_range          = "*"
    destination_port_range     = "1433"
    source_address_prefix      = "*"
    destination_address_prefix = "*"
  }
  security_rule {
    access                  = "Allow"
    description             = "Allow Azure Load Balancer inbound traffic"
    destination_address_prefix =  "10.0.1.0/24"
    destination_address_prefixes = []
    destination_port_range = "*"
    destination_port_ranges = []
    direction = "Inbound"
    name = "Microsoft.Sql-managedInstances_UseOnly_mi-healthprobe-in-10-0-1-0-24-v11"
    priority = 101
    protocol = "*"
    source_address_prefix = "AzureLoadBalancer"
    source_address_prefixes = []
    source_port_range = "*"
    source_port_ranges = []
  }
  security_rule {
    access = "Allow"
    description = "Allow MI internal inbound traffic"
    destination_address_prefix = "10.0.1.0/24"
    destination_address_prefixes = []
    destination_port_range = "*"
    destination_port_ranges = []
    direction = "Inbound"
    name = "Microsoft.Sql-managedInstances_UseOnly_mi-internal-in-10-0-1-0-24-v11"
    priority = 102
    protocol = "*"
    source_address_prefix = "10.0.1.0/24"
    source_address_prefixes = []
    source_port_range = "*"
    source_port_ranges = []
  }
  security_rule {
    access = "Allow"
    description = "Allow communication with Azure Active Directory over https"
    destination_address_prefix = "AzureActiveDirectory"
    destination_address_prefixes = []
    destination_port_range = "443"
    destination_port_ranges = []
    direction = "Outbound"
    name = "Microsoft.Sql-managedInstances_UseOnly_mi-aad-out-10-0-1-0-24-v11"
    priority = 101
    protocol = "Tcp"
    source_address_prefix = "10.0.1.0/24"
    source_address_prefixes = []
    source_port_range = "*"
    source_port_ranges = []
  }
  security_rule {
    access = "Allow"
    description = "Allow communication with the One DS Collector over https"
    destination_address_prefix = "OneDsCollector"
    destination_address_prefixes = []
    destination_port_range = "443"
    destination_port_ranges = []
    direction = "Outbound"
    name = "Microsoft.Sql-managedInstances_UseOnly_mi-onedsc-out-10-0-1-0-24-v11"
    priority = 102
    protocol = "Tcp"
    source_address_prefix = "10.0.1.0/24"
    source_address_prefixes = []
    source_port_range = "*"
    source_port_ranges = []
  }
  security_rule {
    access = "Allow"
    description = "Allow MI internal outbound traffic"
    destination_address_prefix = "10.0.1.0/24"
    destination_address_prefixes = []
    destination_port_range = "*"
    destination_port_ranges = []
    direction = "Outbound"
    name = "Microsoft.Sql-managedInstances_UseOnly_mi-internal-out-10-0-1-0-24-v11"
    priority = 103
    protocol = "*"
    source_address_prefix = "10.0.1.0/24"
    source_address_prefixes = []
    source_port_range = "*"
    source_port_ranges = []
  }
  security_rule {
    access = "Allow"
    description = "Allow outbound communication with storage over HTTPS"
    destination_address_prefix = "Storage.uksouth"
    destination_address_prefixes = []
    destination_port_range = "443"
    destination_port_ranges = []
    direction = "Outbound"
    name = "Microsoft.Sql-managedInstances_UseOnly_mi-strg-p-out-10-0-1-0-24-v11"
    priority = 104
    protocol = "*"
    source_address_prefix = "10.0.1.0/24"
    source_address_prefixes = []
    source_port_range = "*"
    source_port_ranges = []
  }
  security_rule {
    access = "Allow"
    description = "Allow outbound communication with storage over HTTPS"
    destination_address_prefix = "Storage.ukwest"
    destination_address_prefixes = []
    destination_port_range = "443"
    destination_port_ranges = []
    direction = "Outbound"
    name = "Microsoft.Sql-managedInstances_UseOnly_mi-strg-s-out-10-0-1-0-24-v11"
    priority = 105
    protocol = "*"
    source_address_prefix = "10.0.1.0/24"
    source_address_prefixes = []
    source_port_range = "*"
    source_port_ranges = []
  }
  security_rule {
    access = "Allow"
    description = "Allow AzureCloud outbound https traffic"
    destination_address_prefix = "AzureCloud"
    destination_address_prefixes = []
    destination_port_range = "443"
    destination_port_ranges = []
    direction = "Outbound"
    name = "Microsoft.Sql-managedInstances_UseOnly_mi-optional-azure-out-10-0-1-0-24"
    priority = 100
    protocol = "Tcp"
    source_address_prefix = "10.0.1.0/24"
    source_address_prefixes = []
    source_port_range = "*"
    source_port_ranges = []
  }
}

resource "azurerm_route_table" "route_table" {
  name                = "ManagedInstanceRouteTable"
  location            = azurerm_resource_group.learningHubMoodleResourceGroup.location
  resource_group_name = azurerm_resource_group.learningHubMoodleResourceGroup.name
}

resource "azurerm_route" "route" {
  name                        = "ManagedInstanceRoute"
  resource_group_name         = azurerm_resource_group.learningHubMoodleResourceGroup.name
  route_table_name            = azurerm_route_table.route_table.name
  address_prefix              = "0.0.0.0/0"
  next_hop_type               = "VnetLocal"
}


resource "azurerm_subnet" "subnet" {
  name = "ManagedInstanceSubnet"
  resource_group_name = azurerm_resource_group.learningHubMoodleResourceGroup.name
  virtual_network_name = azurerm_virtual_network.vnet.name
  address_prefixes = ["10.0.1.0/24"]
  delegation {
    name = "sqlMI"
    service_delegation {
      name = "Microsoft.Sql/managedInstances"
      actions = ["Microsoft.Network/virtualNetworks/subnets/join/action"]
    }
  }
}

resource "azurerm_subnet_network_security_group_association" "subnet_nsg_association" {
  subnet_id                 = azurerm_subnet.subnet.id
  network_security_group_id = azurerm_network_security_group.nsg.id
}

resource "azurerm_subnet_route_table_association" "subnet_route_table_association" {
  subnet_id = azurerm_subnet.subnet.id
  route_table_id = azurerm_route_table.route_table.id
}

resource "azurerm_mssql_managed_instance" "sqlmi" {
  name = "learninghub-moodle-sql-mi"
  resource_group_name = azurerm_resource_group.learningHubMoodleResourceGroup.name
  location = azurerm_resource_group.learningHubMoodleResourceGroup.location
  license_type = "BasePrice"
  administrator_login = var.SQLAdministratorLogin
  administrator_login_password = var.SQLAdministratorLoginPassword
  subnet_id = azurerm_subnet.subnet.id
  sku_name = var.SQLSkuName
  storage_size_in_gb = var.SQLStorageSize
  vcores = var.SQLVcores
  tags = {
    environment = var.Environment
  }
  identity {
    type = "SystemAssigned"
  }
  lifecycle {
    prevent_destroy = true
  }
}

resource "azurerm_mssql_managed_instance_vulnerability_assessment" "sqlmi_va" {
  managed_instance_id = azurerm_mssql_managed_instance.sqlmi.id
  storage_container_path = "https://${azurerm_storage_account.storage_account.name}.blob.core.windows.net/${azurerm_storage_container.assessment_container.name}"
  storage_account_access_key = azurerm_storage_account.storage_account.primary_access_key
}

resource "azurerm_mssql_managed_database" "sqldb" {
  name = "LearningHubMoodle"
  managed_instance_id = azurerm_mssql_managed_instance.sqlmi.id
}

resource "azurerm_redis_cache" "moodle_cache" {
  name                = "moodle-cache"
  resource_group_name = azurerm_resource_group.learningHubMoodleResourceGroup.name
  location = azurerm_resource_group.learningHubMoodleResourceGroup.location
  capacity            = 2
  family              = "C"
  sku_name            = "Standard"
  non_ssl_port_enabled = false
  minimum_tls_version = "1.2"
}

resource "azurerm_communication_service" "CommunicationService" {
  name                = "CommunicationServiceDev-7312A316-1CCB-4823-B0FB-9146628802E3"
  resource_group_name = azurerm_resource_group.learningHubMoodleResourceGroup.name
  data_location       = "UK"
}

resource "azurerm_email_communication_service" "EmailCommunicationService" {
  name                     = "EmailCommunicationServiceDev"
  resource_group_name      = azurerm_resource_group.learningHubMoodleResourceGroup.name
  data_location            = "UK"
}

resource "azurerm_email_communication_service_domain" "EmailCommunicationServiceDomain" {
  name                = "moodle-dev.test-learninghub.org.uk"
  email_service_id    = azurerm_email_communication_service.EmailCommunicationService.id
  domain_management   = "CustomerManaged"
}