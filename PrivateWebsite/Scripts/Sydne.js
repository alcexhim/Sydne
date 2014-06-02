if (!Sydne)
{
	var Sydne =
	{
		"Available": false,
		"CashDrawer":
		{
			"Available": false,
			"Open": function()
			{
				// alert("The Sydne Monitor Service is not running. Please launch the Sydne Monitor Service and try again.");
			}
		},
		"RFID":
		{
			"Available": false
		}
	};
}

function Sale()
{
	this.Customer = null;
}
function Customer()
{
}
Customer.GetByID = function(id)
{
};