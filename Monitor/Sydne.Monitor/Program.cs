using System;
using System.Collections.Generic;
using System.Linq;
using System.Windows.Forms;

namespace Sydne.Monitor
{
	static class Program
	{
		private static Indigo.Server httpsvr = null;
		private static NotifyIcon nid = new NotifyIcon();

		private static bool mvarResetMifareDevice = false;
		private static ExternalDisplayWindow edw = null;

		/// <summary>
		/// The main entry point for the application.
		/// </summary>
		[STAThread]
		static void Main()
		{
			Application.EnableVisualStyles();
			Application.SetCompatibleTextRenderingDefault(false);

			edw = new ExternalDisplayWindow();
			edw.Show();
			
			Indigo.Protocols.HTTP.HTTPProtocol protocol = new Indigo.Protocols.HTTP.HTTPProtocol();
			Indigo.Services.FileSystem.FileSystemService service = new Indigo.Services.FileSystem.FileSystemService();
			httpsvr = new Indigo.Server(service, protocol);
			httpsvr.Port = 27248;
			service.RequestReceived += new Indigo.Services.FileSystem.FileSystemRequestReceivedEventHandler(service_RequestReceived);
			httpsvr.Start();

			nid.Icon = Properties.Resources.mainicon;
			nid.Text = "Sydne Monitor Service";
			nid.MouseDoubleClick += new MouseEventHandler(nid_MouseDoubleClick);
			nid.ContextMenu = new ContextMenu(
			new MenuItem[]
			{
				new MenuItem("Open Cash &Drawer", new EventHandler(mnuTrayOpenDrawer_Click)),
				new MenuItem("-"),
				new MenuItem("&Settings...", new EventHandler(mnuTraySettings_Click)),
				new MenuItem("-"),
				new MenuItem("&About Sydne Monitor Service", new EventHandler(mnuTrayAbout_Click)),
				new MenuItem("E&xit", new EventHandler(mnuTrayExit_Click))
			});
			nid.ContextMenu.MenuItems[0].DefaultItem = true;
			nid.Visible = true;

			Application.Run();

			nid.Visible = false;
		}

		static void nid_MouseDoubleClick(object sender, MouseEventArgs e)
		{
			OpenCashDrawer();
		}

		private static void mnuTrayOpenDrawer_Click(object sender, EventArgs e)
		{
			OpenCashDrawer();
		}

		static CashDrawerResult OpenCashDrawer()
		{
			/*
			LibUsbDotNet.Main.UsbDeviceFinder finder = new LibUsbDotNet.Main.UsbDeviceFinder(0x0451, 0x0909);
			LibUsbDotNet.UsbDevice device = LibUsbDotNet.UsbDevice.OpenUsbDevice(finder);

			LibUsbDotNet.IUsbDevice iusb = (device as LibUsbDotNet.IUsbDevice);
			if (iusb != null)
			{
				iusb.SetConfiguration(1);
				iusb.ClaimInterface(0);
			}

			LibUsbDotNet.UsbEndpointWriter writer = device.OpenEndpointWriter(LibUsbDotNet.Main.WriteEndpointID.Ep01);
			int transferLength = 0;

			System.IO.MemoryStream ms = new System.IO.MemoryStream();
			UniversalEditor.IO.BinaryWriter bw = new UniversalEditor.IO.BinaryWriter(ms);
			bw.WriteFixedLengthString("a\r\n");
			bw.Close();
			byte[] data = ms.ToArray();

			writer.Write(data, 1000, out transferLength);
			writer.Flush();

			device.Close();
			*/

			System.IO.Ports.SerialPort port = new System.IO.Ports.SerialPort("COM1");
			port.Open();

			bool p = port.IsOpen;
			port.ReadTimeout = 1000;

			int status = port.ReadChar();
			CashDrawerResult result = CashDrawerResult.Undefined;
			if (status == 1)
			{
				port.Write("a");

				// close and reopen the port to reset - otherwise the driver
				// caches the result of the next status read (probably because it
				// assumes the drawer opened successfully after an attempt to open it?)
				port.Close();
				port.Open();
				
				System.Threading.Thread.Sleep(100);
				status = port.ReadChar();
				if (status == 1)
				{
					// cash drawer is closed
					nid.ShowBalloonTip(3000, "Cash drawer did not open", "The open command was sent, but the cash drawer reported that it is still closed. Is there something blocking the tray?", ToolTipIcon.Error);
					result = CashDrawerResult.Locked;
				}
				else
				{
					// cash drawer is open
					nid.ShowBalloonTip(3000, "Cash drawer opened", "Please accept the customer's payment and issue the correct change. Don't forget to thank them for their business!", ToolTipIcon.Info);
					result = CashDrawerResult.Opened;
				}
			}
			else
			{
				// cash drawer is open
				nid.ShowBalloonTip(3000, "Cash drawer did not open", "The cash drawer reported that it is already open.", ToolTipIcon.Error);
				result = CashDrawerResult.AlreadyOpened;
			}
			
			port.Close();
			return result;
		}
		static void CloseCashDrawer()
		{
			MessageBox.Show("The cash drawer was closed.", "Information", MessageBoxButtons.OK, MessageBoxIcon.Information);
		}

		static void service_RequestReceived(object sender, Indigo.Services.FileSystem.FileSystemRequestReceivedEventArgs e)
		{
			Indigo.Protocols.HTTP.HTTPProtocol http = (e.Client.Protocol as Indigo.Protocols.HTTP.HTTPProtocol);

			string pathName = e.ObjectName;
			string queryString = String.Empty;
			if (e.ObjectName.Contains("?"))
			{
				pathName = e.ObjectName.Substring(0, e.ObjectName.IndexOf("?"));
				queryString = e.ObjectName.Substring(e.ObjectName.IndexOf("?") + 1);
			}

			Dictionary<string, string> queryStringValues = new Dictionary<string, string>();
			string[] queryStringSplit = queryString.Split(new char[] { '&' });
			foreach (string queryStringParam in queryStringSplit)
			{
				string[] queryStringParamNameAndValue = queryStringParam.Split(new char[] { '=' }, 2);
				string queryStringParamName = queryStringParamNameAndValue[0];
				string queryStringParamValue = String.Empty;
				if (queryStringParamNameAndValue.Length > 1)
				{
					queryStringParamValue = queryStringParamNameAndValue[1];
				}

				queryStringValues[queryStringParamName] = queryStringParamValue;
			}
			if (queryStringValues.ContainsKey("jsonp"))
			{
				http.Response.TextWriter.Write(queryStringValues["jsonp"] + "(");
			}
			switch (pathName)
			{
				#region Sydne.js
				case "/Sydne.js":
				{
					http.Response.Headers.Add(Indigo.Protocols.HTTP.HTTPResponseHeaderType.ContentType, "text/plain");

					http.Response.TextWriter.WriteLine("if (typeof XMLHttpRequest === \"undefined\") { XMLHttpRequest = function () { try { return new ActiveXObject(\"Msxml2.XMLHTTP.6.0\"); } catch (e) {} try { return new ActiveXObject(\"Msxml2.XMLHTTP.3.0\"); } catch (e) {} try { return new ActiveXObject(\"Microsoft.XMLHTTP\"); } catch (e) {} throw new Error(\"This browser does not support XMLHttpRequest.\"); }; }");
					http.Response.TextWriter.Write("var Sydne = { ");
					http.Response.TextWriter.Write("\"Available\": true,");
					
					http.Response.TextWriter.Write("\"CashDrawer\": { ");
					http.Response.TextWriter.Write("\"Open\": function() { ");
					http.Response.TextWriter.Write("try { ");
					http.Response.TextWriter.Write("var xhr = new XMLHttpRequest(); xhr.open('GET', 'http://localhost:27248/CashDrawer/Open', false); xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); xhr.send(null);");
					http.Response.TextWriter.Write("} catch (ex) { }");
					http.Response.TextWriter.Write(" }");
					http.Response.TextWriter.Write(" }, ");

					http.Response.TextWriter.Write("\"ExternalDisplay\": { ");
					http.Response.TextWriter.Write("\"LogoText\": function(text) { ");
					http.Response.TextWriter.Write("try { ");
					http.Response.TextWriter.Write("var xhr = new XMLHttpRequest(); xhr.open('GET', 'http://localhost:27248/ExternalDisplay/LogoText?text=' + JH.Utilities.UrlEncode(text), true); xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); xhr.send(null);");
					http.Response.TextWriter.Write("} catch (ex) { }");
					http.Response.TextWriter.Write(" }, ");
					http.Response.TextWriter.Write("\"SaleDetail\": function(text) { ");
					http.Response.TextWriter.Write("try { ");
					http.Response.TextWriter.Write("var xhr = new XMLHttpRequest(); xhr.open('GET', 'http://localhost:27248/ExternalDisplay/SaleDetail?text=' + JH.Utilities.UrlEncode(text), true); xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); xhr.send(null);");
					http.Response.TextWriter.Write("} catch (ex) { }");
					http.Response.TextWriter.Write(" }, ");
					http.Response.TextWriter.Write("\"SaleFinal\": function(subtotal, tax, tendered) { ");
					http.Response.TextWriter.Write("try { ");
					http.Response.TextWriter.Write("var xhr = new XMLHttpRequest(); xhr.open('GET', 'http://localhost:27248/ExternalDisplay/SaleFinal?subtotal=' + subtotal + '&tax=' + tax + '&tendered=' + tendered, true); xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); xhr.send(null);");
					http.Response.TextWriter.Write("} catch (ex) { alert(ex); }");
					http.Response.TextWriter.Write(" } ");
					http.Response.TextWriter.Write(" } ");

					/*
					http.Response.TextWriter.Write("\"Mifare\": { ");
					http.Response.TextWriter.Write("\"Scan\": function() { ");

					http.Response.TextWriter.Write("var script = document.createElement(\"SCRIPT\");");
					http.Response.TextWriter.Write("script.setAttribute(\"type\", \"text/javascript\");");
					http.Response.TextWriter.Write("script.setAttribute(\"src\", \"http://localhost:27248/Mifare/Scan?jsonp=__AJAX2103_ProcessLogin\");");
					http.Response.TextWriter.Write("script.onload = function() { if (!loginSuccessful) tryLogin(); }; document.body.appendChild(script);");

					http.Response.TextWriter.Write(" }");
					http.Response.TextWriter.Write(" }");
					*/

					http.Response.TextWriter.Write(" };");
					break;
				}
				#endregion
				#region Cash Drawer
				case "/CashDrawer/Open":
				{
					http.Response.Headers.Add(Indigo.Protocols.HTTP.HTTPResponseHeaderType.ContentType, "text/plain");
					try
					{
						CashDrawerResult result = OpenCashDrawer();
						switch (result)
						{
							case CashDrawerResult.Opened:
							{
								http.Response.TextWriter.Write("{ \"result\": \"success\", \"status\": \"Opened\" }");
								break;
							}
							case CashDrawerResult.AlreadyOpened:
							{
								http.Response.TextWriter.Write("{ \"result\": \"success\", \"status\": \"AlreadyOpened\" }");
								break;
							}
							case CashDrawerResult.Locked:
							{
								http.Response.TextWriter.Write("{ \"result\": \"success\", \"status\": \"Locked\" }");
								break;
							}
						}
					}
					catch (Exception ex)
					{
						http.Response.TextWriter.Write("{ \"result\": \"failure\", \"errorType\": \"" + ex.GetType().FullName + "\", \"errorMessage\": \"" + ex.Message + "\" }");
					}
					break;
				}
				/*
				case "/CashDrawer/Close":
				{
					try
					{
						CloseCashDrawer();
						http.Response.TextWriter.Write("{ \"result\": \"success\", \"status\": \"closed\" }");
					}
					catch (Exception ex)
					{
						http.Response.TextWriter.Write("{ \"result\": \"failure\", \"errorType\": \"" + ex.GetType().FullName + "\", \"errorMessage\": \"" + ex.Message + "\" }");
					}
					break;
				}
				*/
				#endregion
				#region RFID Reader
				case "/Mifare/Scan":
				{
					http.Response.Headers.Add(Indigo.Protocols.HTTP.HTTPResponseHeaderType.ContentType, "text/plain");
					try
					{
						Mifare.Device device = new Mifare.Device();
						DateTime time = DateTime.Now;
						int timeoutMilliseconds = 5000;

						byte[] data = null;
						while (!mvarResetMifareDevice)
						{
							try
							{
								device.Blink(8, 1);
								data = device.Read(Mifare.DataEncoding.S50S70);
								break;
							}
							catch (Mifare.CardNotPresentException ex)
							{
							}
							catch (Mifare.DeviceNotPresentException ex)
							{
								http.Response.TextWriter.Write("{ \"result\": \"failure\", \"message\": \"The card reader is not connected.\", \"remedy\": \"ConnectDevice\" }");
								break;
							}
							catch (Exception ex)
							{
								http.Response.TextWriter.Write("{ \"result\": \"failure\", \"message\": \"" + ex.Message + "\" }");
								break;
							}
							/*
							TimeSpan ts = DateTime.Now.Subtract(time);
							if (ts.TotalMilliseconds > timeoutMilliseconds)
							{
								http.Response.TextWriter.Write("{ \"result\": \"failure\", \"message\": \"No card present\" }");
								break;
							}
							*/

							if (!e.Client.IsConnected)
							{
								break;
							}
						}
						mvarResetMifareDevice = false;

						if (data != null)
						{
							string w = System.Text.Encoding.ASCII.GetString(data);
							if (w.Contains("\0")) w = w.Substring(0, w.IndexOf("\0"));

							/*
							if (!w.Contains(" "))
							{
								http.Response.TextWriter.Write("{ \"result\": \"failure\", \"message\": \"Invalid credential card\" }");
								break;
							}
							*/

							device.Chirp(5, 1);
							http.Response.TextWriter.Write("{ \"result\": \"success\", \"content\": \"" + JH.Utilities.JavaScriptEncode(w, "\"") + "\" }");
							break;
						}

					}
					catch (Exception ex)
					{
						http.Response.TextWriter.Write("{ \"result\": \"failure\", \"errorType\": \"" + ex.GetType().FullName + "\", \"errorMessage\": \"" + ex.Message + "\" }");
					}
					break;
				}
				case "/Mifare/Reset":
				{
					mvarResetMifareDevice = true;
					break;
				}
				/*
				case "/CashDrawer/Close":
				{
					try
					{
						CloseCashDrawer();
						http.Response.TextWriter.Write("{ \"result\": \"success\", \"status\": \"closed\" }");
					}
					catch (Exception ex)
					{
						http.Response.TextWriter.Write("{ \"result\": \"failure\", \"errorType\": \"" + ex.GetType().FullName + "\", \"errorMessage\": \"" + ex.Message + "\" }");
					}
					break;
				}
				*/
				#endregion
				#region External Display
				case "/ExternalDisplay/LogoText":
				{
					SetEDWMode(ExternalDisplayMode.LogoText);

					string text = (e.Client.Protocol as Indigo.Protocols.HTTP.HTTPProtocol).Request.QueryString["text"];
					text = JH.Utilities.UrlDecode(text);

					SetEDWText(text);
					http.Response.TextWriter.Write("{ \"result\": \"success\" }");
					break;
				}
				case "/ExternalDisplay/SaleDetail":
				{
					SetEDWMode(ExternalDisplayMode.SaleDetail);

					string text = (e.Client.Protocol as Indigo.Protocols.HTTP.HTTPProtocol).Request.QueryString["text"];
					text = JH.Utilities.UrlDecode(text);

					SetEDWText(text);
					http.Response.TextWriter.Write("{ \"result\": \"success\" }");
					break;
				}
				case "/ExternalDisplay/SaleFinal":
				{
					SetEDWMode(ExternalDisplayMode.SaleFinal);

					string sSubtotal = http.Request.QueryString["subtotal"];
					double dSubtotal = 0.00;
					Double.TryParse(sSubtotal, out dSubtotal);

					string sTax = http.Request.QueryString["tax"];
					double dTax = 0.00;
					Double.TryParse(sTax, out dTax);

					string sTendered = http.Request.QueryString["tendered"];
					double dTendered = 0.00;
					Double.TryParse(sTendered, out dTendered);

					SetEDWFinal(dSubtotal, dTax, dTendered);
					http.Response.TextWriter.Write("{ \"result\": \"success\" }");
					break;
				}
				#endregion
				#region Unknown Command
				default:
				{
					http.Response.TextWriter.Write("{ \"result\": \"failure\", \"errorMessage\": \"Unknown command\" }");
					break;
				}
				#endregion
			}
			if (queryStringValues.ContainsKey("jsonp"))
			{
				http.Response.TextWriter.Write(");");
			}
			http.Response.TextWriter.Flush();
			e.PreventDefault = true;
		}

		private static void SetEDWFinal(double dSubtotal, double dTax, double dTendered)
		{
			edw.Invoke(new Action<double, double, double>(_SetEDWFinal), dSubtotal, dTax, dTendered);
		}
		private static void _SetEDWFinal(double dSubtotal, double dTax, double dTendered)
		{
			edw.FinalParameters = new double[] { dSubtotal, dTax, dTendered };
		}

		private static void SetEDWMode(ExternalDisplayMode mode)
		{
			edw.Invoke(new Action<ExternalDisplayMode>(_SetEDWMode), mode);
		}
		private static void _SetEDWMode(ExternalDisplayMode mode)
		{
			edw.Mode = mode;
		}

		private static void SetEDWText(string p)
		{
			edw.Invoke(new Action<string>(_SetEDWText), p);
		}
		private static void _SetEDWText(string p)
		{
			edw.Content = p;
		}

		private static void mnuTraySettings_Click(object sender, EventArgs e)
		{
			SettingsDialog dlg = new SettingsDialog();
			if (dlg.ShowDialog() == DialogResult.OK)
			{
			}
		}
		private static void mnuTrayAbout_Click(object sender, EventArgs e)
		{
			MessageBox.Show("Cash Drawer Monitor\r\nVersion " + System.Reflection.Assembly.GetExecutingAssembly().GetName().Version.ToString() + "\r\nWritten by Michael Becker\r\n\r\nLicensed under the GNU General Public License, Cash Drawer Monitor is free software.", "Information", MessageBoxButtons.OK, MessageBoxIcon.Information);
		}
		private static void mnuTrayExit_Click(object sender, EventArgs e)
		{
			if (httpsvr != null)
			{
				httpsvr.Stop();
			}
			Application.Exit();
		}
	}
}
