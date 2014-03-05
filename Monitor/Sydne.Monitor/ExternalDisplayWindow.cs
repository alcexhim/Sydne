using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace Sydne.Monitor
{
	public partial class ExternalDisplayWindow : Form
	{
		private ExternalDisplayMode mvarMode = ExternalDisplayMode.LogoText;
		public ExternalDisplayMode Mode { get { return mvarMode; } set { mvarMode = value; Refresh(); } }

		public ExternalDisplayWindow()
		{
			InitializeComponent();
			
			DoubleBuffered = true;

			BackColor = Color.FromArgb(187, 119, 238);
			ForeColor = Color.FromArgb(51, 0, 51);

			AutoScaleMode = System.Windows.Forms.AutoScaleMode.None;
			Font = new Font("Segoe UI Light", 24, FontStyle.Regular);

			if (System.IO.File.Exists("Images/Companies/1/Logo.png"))
			{
				mvarLogoImage = Image.FromFile("Images/Companies/1/Logo.png");
			}
			Content = "Ready";
		}

		private string mvarContent = String.Empty;
		public string Content
		{
			get { return mvarContent; }
			set { mvarContent = value; Refresh(); }
		}

		private Image mvarLogoImage = null;

		private bool mvarFullScreen = false;
		public bool FullScreen
		{
			get { return mvarFullScreen; }
			set
			{
				if (value)
				{
					this.WindowState = FormWindowState.Maximized;
					this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None;
				}
				else
				{
					this.WindowState = FormWindowState.Normal;
					this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.Sizable;
				}
				mvarFullScreen = value;
			}
		}

		protected override void OnResize(EventArgs e)
		{
			base.OnResize(e);

			if (WindowState == FormWindowState.Maximized)
			{
				FullScreen = true;
			}
			Refresh();
		}

		protected override void OnKeyDown(KeyEventArgs e)
		{
			base.OnKeyDown(e);
			if (e.KeyCode == Keys.Enter && e.Alt)
			{
				FullScreen = !FullScreen;
			}
		}

		protected override void OnPaint(PaintEventArgs e)
		{
			base.OnPaint(e);

			switch (mvarMode)
			{
				case ExternalDisplayMode.LogoText:
				{
					if (mvarLogoImage != null)
					{
						e.Graphics.DrawImage(mvarLogoImage, new Rectangle((Width - mvarLogoImage.Width) / 2, ((Height - mvarLogoImage.Height) / 2) - 48, mvarLogoImage.Width, mvarLogoImage.Height));
					}
					RenderCenteredText(e.Graphics, Content);
					break;
				}
				case ExternalDisplayMode.SaleDetail:
				{
					// To display on a sale detail:
					// http://localhost:27248/ExternalDisplay/SaleDetail?text={quantity}%09{title}%09{price}%0A{repeat}

					if (mvarLogoImage != null)
					{
						double scaleFactor = (double)128 / mvarLogoImage.Width;
						int width = 128, height = (int)(scaleFactor * mvarLogoImage.Height);

						e.Graphics.DrawImage(mvarLogoImage, new Rectangle(8, 8, width, height));
					}

					string[] lines = Content.Split(new char[] { '\n' });

					double lineTotal = 0.00;
					for (int i = 0; i < lines.Length; i++)
					{
						string[] split = lines[i].Split(new char[] { '\t' });
						if (split.Length > 2)
						{
							double value = 0.00;
							if (Double.TryParse(split[2], out value))
							{
								lineTotal += value;
							}
						}
					}

					RenderSaleTotal(e.Graphics, lineTotal.ToString("C"), "Total: ");

					int j = 0;
					for (int i = 0; i < lines.Length; i++)
					{
						if (String.IsNullOrEmpty(lines[i])) continue;

						RenderSaleLine(e.Graphics, lines[i], j);
						j++;
					}

					break;
				}
				case ExternalDisplayMode.SaleFinal:
				{
					// To display on a sale final:
					// http://localhost:27248/ExternalDisplay/SaleFinal?total=0.00&tax=0.00&tendered=0.00

					if (mvarLogoImage != null)
					{
						double scaleFactor = (double)128 / mvarLogoImage.Width;
						int width = 128, height = (int)(scaleFactor * mvarLogoImage.Height);

						e.Graphics.DrawImage(mvarLogoImage, new Rectangle(8, 8, width, height));
					}

					RenderSaleFinal(e.Graphics);
					break;
				}
			}
		}

		private void RenderSaleFinal(Graphics graphics)
		{
			TextFormatFlags flags = TextFormatFlags.Right;
			Font fontLight = new Font("Segoe UI Light", 20, FontStyle.Regular);
			Font fontRegular = new Font("Segoe UI", 20, FontStyle.Regular);
			Font font = new Font("Segoe UI", 24, FontStyle.Bold);

			int t = 32;
			int y = 32;
			TextRenderer.DrawText(graphics, "Subtotal: ", fontLight, new Rectangle(0, y, this.Width - 200, this.Height), ForeColor, flags);
			TextRenderer.DrawText(graphics, mvarFinalParameters[0].ToString("C"), fontLight, new Rectangle(0, y, this.Width - 32, this.Height), ForeColor, flags);
			
			y += t;
			TextRenderer.DrawText(graphics, "Tax: ", fontLight, new Rectangle(0, y, this.Width - 200, this.Height), ForeColor, flags);
			TextRenderer.DrawText(graphics, mvarFinalParameters[1].ToString("C"), fontLight, new Rectangle(0, y, this.Width - 32, this.Height), ForeColor, flags);
			
			y += t;
			TextRenderer.DrawText(graphics, "Total: ", fontLight, new Rectangle(0, y, this.Width - 200, this.Height), ForeColor, flags);
			TextRenderer.DrawText(graphics, (mvarFinalParameters[0] + mvarFinalParameters[1]).ToString("C"), fontLight, new Rectangle(0, y, this.Width - 32, this.Height), ForeColor, flags);

			y += t;
			TextRenderer.DrawText(graphics, "Tendered: ", fontRegular, new Rectangle(0, y, this.Width - 200, this.Height), ForeColor, flags);
			TextRenderer.DrawText(graphics, mvarFinalParameters[2].ToString("C"), fontRegular, new Rectangle(0, y, this.Width - 32, this.Height), ForeColor, flags);

			t = 48;
			y += t;
			TextRenderer.DrawText(graphics, "Change: ", font, new Rectangle(0, y, this.Width - 200, this.Height), ForeColor, flags);
			TextRenderer.DrawText(graphics, (mvarFinalParameters[2] - (mvarFinalParameters[0] + mvarFinalParameters[1])).ToString("C"), font, new Rectangle(0, y, this.Width - 32, this.Height), ForeColor, flags);
			
			flags = TextFormatFlags.HorizontalCenter | TextFormatFlags.VerticalCenter;
			TextRenderer.DrawText(graphics, "Thank you, come again!", Font, new Rectangle(0, 96, this.Width, this.Height), ForeColor, flags);
		}

		private void RenderCenteredText(Graphics g, string text)
		{
			TextFormatFlags flags = TextFormatFlags.HorizontalCenter | TextFormatFlags.VerticalCenter;
			TextRenderer.DrawText(g, text, Font, new Rectangle(0, 96, this.Width, this.Height), ForeColor, flags);
		}
		private void RenderSaleTotal(Graphics g, string text, string label)
		{
			TextFormatFlags flags = TextFormatFlags.Right;
			Font font = new Font("Segoe UI", 24, FontStyle.Bold);
			TextRenderer.DrawText(g, label, font, new Rectangle(0, 32, this.Width - 200, this.Height), ForeColor, flags);

			TextRenderer.DrawText(g, text, font, new Rectangle(0, 32, this.Width - 32, this.Height), ForeColor, flags);
		}
		private void RenderSaleLine(Graphics g, string text, int index)
		{
			TextFormatFlags flags = TextFormatFlags.Left;
			Font font = new Font("Segoe UI", 14, FontStyle.Regular);

			string[] parts = text.Split(new char[] { '\t' });
			if (parts.Length > 0)
			{
				int lineHeight = 32;
				flags = TextFormatFlags.Right;
				TextRenderer.DrawText(g, parts[0], font, new Rectangle(0, 128 + (index * lineHeight), 96, this.Height), ForeColor, flags);
				if (parts.Length > 1)
				{
					flags = TextFormatFlags.Left;
					TextRenderer.DrawText(g, parts[1], font, new Rectangle(128, 128 + (index * lineHeight), this.Width - 240, this.Height), ForeColor, flags);
					if (parts.Length > 2)
					{
						flags = TextFormatFlags.Right;

						double value = 0.00;
						if (!Double.TryParse(parts[2], out value))
						{
							TextRenderer.DrawText(g, parts[2], font, new Rectangle(0, 128 + (index * lineHeight), this.Width - 32, this.Height), ForeColor, flags);
						}
						else
						{
							TextRenderer.DrawText(g, value.ToString("C"), font, new Rectangle(0, 128 + (index * lineHeight), this.Width - 32, this.Height), ForeColor, flags);
						}
					}
				}
			}
		}

		private double[] mvarFinalParameters = new double[3];
		public double[] FinalParameters { get { return mvarFinalParameters; } set { mvarFinalParameters = value; Refresh(); } }
	}
}
