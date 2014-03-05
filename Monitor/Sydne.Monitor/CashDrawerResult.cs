using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Sydne.Monitor
{
    public enum CashDrawerResult
    {
        Undefined = -1,
        Closed = 0,
        Opened = 1,
        Locked = 2,
        AlreadyOpened = 3
    }
}
