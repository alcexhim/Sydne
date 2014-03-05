function Keypad(id)
{
	this.ID = id;
	this.Backspace = function()
	{
		var v = this.GetValue();
		this.SetValue(v.substring(0, v.length - 1));
	};
	this.Decimal = function()
	{
		this.SetValue(this.GetValue() + ".");
	};
	this.Postback = function()
	{
		var keypad_Form = document.getElementById("Keypad_" + this.ID + "_Form");
		keypad_Form.submit();
	};
	this.GetValue = function()
	{
		var keypad_Value = document.getElementById("Keypad_" + this.ID + "_Value");
		return keypad_Value.value;
	};
	this.SetValue = function(value, postback)
	{
		var keypad_Value = document.getElementById("Keypad_" + this.ID + "_Value");
		keypad_Value.value = value;
		
		var keypad_ValueText = document.getElementById("Keypad_" + this.ID + "_ValueText");
		keypad_ValueText.innerHTML = keypad_Value.value;
		
		if (postback) this.Postback();
	};
	this.QuickPreset = function(value)
	{
		this.SetValue(parseFloat(this.GetValue()) + parseFloat(value));
	};
	this.Press = function(value)
	{
		this.SetValue(this.GetValue() + value);
	};
}