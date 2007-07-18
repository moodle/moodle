/**
 * Code Syntax Highlighter for C++(Windows Platform).
 * Version 0.0.2
 * Copyright (C) 2006 Shin, YoungJin.
 * http://www.jiniya.net/lecture/techbox/test.html
 * 
 * This library is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General 
 * Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) 
 * any later version.
 *
 * This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more 
 * details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to 
 * the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA 
 */

dp.sh.Brushes.Cpp = function()
{
	var datatypes = 
	'ATOM BOOL BOOLEAN BYTE CHAR COLORREF DWORD DWORDLONG DWORD_PTR ' +
	'DWORD32 DWORD64 FLOAT HACCEL HALF_PTR HANDLE HBITMAP HBRUSH ' + 
	'HCOLORSPACE HCONV HCONVLIST HCURSOR HDC HDDEDATA HDESK HDROP HDWP ' +
	'HENHMETAFILE HFILE HFONT HGDIOBJ HGLOBAL HHOOK HICON HINSTANCE HKEY ' +
	'HKL HLOCAL HMENU HMETAFILE HMODULE HMONITOR HPALETTE HPEN HRESULT ' +
	'HRGN HRSRC HSZ HWINSTA HWND INT INT_PTR INT32 INT64 LANGID LCID LCTYPE ' +
	'LGRPID LONG LONGLONG LONG_PTR LONG32 LONG64 LPARAM LPBOOL LPBYTE LPCOLORREF ' +
	'LPCSTR LPCTSTR LPCVOID LPCWSTR LPDWORD LPHANDLE LPINT LPLONG LPSTR LPTSTR ' +
	'LPVOID LPWORD LPWSTR LRESULT PBOOL PBOOLEAN PBYTE PCHAR PCSTR PCTSTR PCWSTR ' +
	'PDWORDLONG PDWORD_PTR PDWORD32 PDWORD64 PFLOAT PHALF_PTR PHANDLE PHKEY PINT ' +
	'PINT_PTR PINT32 PINT64 PLCID PLONG PLONGLONG PLONG_PTR PLONG32 PLONG64 POINTER_32 ' +
	'POINTER_64 PSHORT PSIZE_T PSSIZE_T PSTR PTBYTE PTCHAR PTSTR PUCHAR PUHALF_PTR ' +
	'PUINT PUINT_PTR PUINT32 PUINT64 PULONG PULONGLONG PULONG_PTR PULONG32 PULONG64 ' +
	'PUSHORT PVOID PWCHAR PWORD PWSTR SC_HANDLE SC_LOCK SERVICE_STATUS_HANDLE SHORT ' + 
	'SIZE_T SSIZE_T TBYTE TCHAR UCHAR UHALF_PTR UINT UINT_PTR UINT32 UINT64 ULONG ' +
	'ULONGLONG ULONG_PTR ULONG32 ULONG64 USHORT USN VOID WCHAR WORD WPARAM WPARAM WPARAM ' +
	'char bool short int __int32 __int64 __int8 __int16 long float double __wchar_t ' +
	'clock_t _complex _dev_t _diskfree_t div_t ldiv_t _exception _EXCEPTION_POINTERS ' +
	'FILE _finddata_t _finddatai64_t _wfinddata_t _wfinddatai64_t __finddata64_t ' +
	'__wfinddata64_t _FPIEEE_RECORD fpos_t _HEAPINFO _HFILE lconv intptr_t ' +
	'jmp_buf mbstate_t _off_t _onexit_t _PNH ptrdiff_t _purecall_handler ' +
	'sig_atomic_t size_t _stat __stat64 _stati64 terminate_function ' +
	'time_t __time64_t _timeb __timeb64 tm uintptr_t _utimbuf ' +
	'va_list wchar_t wctrans_t wctype_t wint_t signed';

	var keywords = 
	'break case catch class const __finally __exception __try ' +
	'const_cast continue private public protected __declspec ' + 
	'default delete deprecated dllexport dllimport do dynamic_cast ' + 
	'else enum explicit extern if for friend goto inline ' + 
	'mutable naked namespace new noinline noreturn nothrow ' + 
	'register reinterpret_cast return selectany ' + 
	'sizeof static static_cast struct switch template this ' + 
	'thread throw true false try typedef typeid typename union ' + 
	'using uuid virtual void volatile whcar_t while';

	this.regexList = [
		{ regex: dp.sh.RegexLib.SingleLineCComments,				css: 'comment' },			// one line comments
		{ regex: dp.sh.RegexLib.MultiLineCComments,					css: 'comment' },			// multiline comments
		{ regex: dp.sh.RegexLib.DoubleQuotedString,					css: 'string' },			// strings
		{ regex: dp.sh.RegexLib.SingleQuotedString,					css: 'string' },			// strings
		{ regex: new RegExp('^ *#.*', 'gm'),						css: 'preprocessor' },
		{ regex: new RegExp(this.GetKeywords(datatypes), 'gm'),		css: 'datatypes' },
		{ regex: new RegExp(this.GetKeywords(keywords), 'gm'),		css: 'keyword' }
		];

	this.CssClass = 'dp-cpp';
	this.Style =	'.dp-cpp .datatypes { color: #2E8B57; font-weight: bold; }';
}

dp.sh.Brushes.Cpp.prototype	= new dp.sh.Highlighter();
dp.sh.Brushes.Cpp.Aliases	= ['cpp', 'c', 'c++'];
