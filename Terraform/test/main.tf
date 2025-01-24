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

resource "azurerm_kubernetes_cluster" "aks" {
  name                = var.ClusterName
  location            = azurerm_resource_group.learningHubMoodleResourceGroup.location
  resource_group_name = azurerm_resource_group.learningHubMoodleResourceGroup.name
  dns_prefix          = var.ClusterName
  default_node_pool {
    name       = "default"
    node_count = var.ClusterNodeCount
    vm_size    = var.ClusterNodeSize
  }
  identity {
    type = "SystemAssigned"
  }
  network_profile {
    network_plugin = "azure"
  }
  tags = {
    environment = var.Environment
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
}

resource "azurerm_mssql_managed_database" "sqldb" {
  name = "LearningHubMoodle"
  managed_instance_id = azurerm_mssql_managed_instance.sqlmi.id
}