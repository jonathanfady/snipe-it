<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!-- saved from url=(0067)http://s3.pdfconvertonline.com/convert/p3r68-cdx67/eocl1-lygr0.html -->
<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <title>RESPONSIBILITY FORM {{$asset->asset_tag}}</title>

        <meta name="generator" content="LibreOffice 5.4.7.2 (Linux)">
        <meta name="author" content="itsol">
        <meta name="created" content="2021-02-11T07:49:57">
        <meta name="changedby" content="Jonathan FADY">
        <meta name="changed" content="2021-02-17T18:09:41">
        <meta name="AppVersion" content="16.0300">
        <meta name="DocSecurity" content="0">
        <meta name="HyperlinksChanged" content="false">
        <meta name="LinksUpToDate" content="false">
        <meta name="ScaleCrop" content="false">
        <meta name="ShareDoc" content="false">

        <style type="text/css">
            @page {
                margin: 10px 20px;
                font-family: Calibri, sans-serif;
                font-size: 9;
            }

            table {
                border-collapse: collapse;
                margin: 10px 0px;
                width: 100%;
            }

            td {
                padding: 5px;
                vertical-align: top;
            }
        </style>

    </head>

    <body>
        <div align="center">
            <img src="{{ public_path() }}/img/solidarites-international.png" width="93" height="82">
        </div>
        <h2 align="center" style="background:#D9D9D9">
            <b>RESPONSIBILITY FORM</b>
        </h2>
        <table>
            <tr>
                <td>
                    Country/Mission
                </td>
                <td width="30%">
                    <b>Lebanon</b>
                </td>
                <td>
                    Name of the requester
                </td>
                <td width="30%">
                    <b>{{ $user ? $user->getFullNameAttribute() : '' }}</b>
                </td>
            </tr>
            <tr>
                <td>
                    Base
                </td>
                <td>
                    <b>{{ $asset->location ? $asset->location->name : ($asset->defaultLoc ? $asset->defaultLoc->name : '') }}</b>
                </td>
                <td>
                    Position
                </td>
                <td>
                    <b>{{ $user ? $user->jobtitle : '' }}</b>
                </td>
            </tr>
            <tr>
                <td>
                    Date
                </td>
                <td>
                    <b>{{ date('Y-m-d')}}</b>
                </td>
            </tr>
        </table>
        <table>
            <tr style="background:#D9D9D9">
                <th style="border: 1px solid #000000">
                    Designation
                </th>
                <th style="border: 1px solid #000000">
                    Brand/Model
                </th>
                <th style="border: 1px solid #000000">
                    Logistics Code
                </th>
                <th style="border: 1px solid #000000">
                    Serial Number / IMEI
                </th>
                <th style="border: 1px solid #000000">
                    Equipment Conditions / Pack
                </th>
            </tr>
            <tr>
                <td style="border: 1px solid #000000">
                    <b>{{ ($asset->model && $asset->model->category) ? $asset->model->category->name : '' }}</b>
                </td>
                <td style="border: 1px solid #000000">
                    <b>{{ $asset->model ? $asset->model->name : '' }}</b>
                </td>
                <td style="border: 1px solid #000000">
                    <b>{{ $asset->asset_tag }}</b>
                </td>
                <td style="border: 1px solid #000000">
                    <b>{{ $asset->serial }}</b>
                </td>
                <td style="border: 1px solid #000000">
                    <b></b>
                </td>
            </tr>
        </table>
        <p>
            <b>The equipment does not belong to you and is valuable to the association. In consequence you must take
                care of it.</b><br>
            In case of a loss, equipment robbery or damage, the coordination team will decide the responsibility degree
            and if necessary you will reimburse a part or the total value of the equipment.
        </p>
        <table>
            <tr>
                <td colspan="2" style="border: 1px solid #000000">
                    I, the undersigned, certify to take full responsibility for the above-mentioned equipment.
                </td>
                <td style="border: 1px solid #000000">
                    Logistics Coordinator / Logistician
                </td>
                <td style="border: 1px solid #000000">
                    <b>{{ $asset->focal_point ? $asset->focal_point->getFullNameAttribute() : '' }}</b>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000000">
                    Name and signature
                </td>
                <td width="30%" height="60px" style="border: 1px solid #000000">
                    <b>{{ $user ? $user->getFullNameAttribute() : '' }}</b>
                </td>
                <td style="border: 1px solid #000000">
                    Name and signature
                </td>
                <td width="30%" style="border: 1px solid #000000">
                    <b>{{ $asset->focal_point ? $asset->focal_point->getFullNameAttribute() : '' }}</b>
                </td>
            </tr>
        </table>
        <h3 align="center" style="background:#D9D9D9">Return</h3>
        <table style="width:50%">
            <tr>
                <td>
                    Date of the equipment return
                </td>
                <td>
                    <b>{{ $asset->expected_checkin}}</b>
                </td>
            </tr>
        </table>
        <h4 align="center">
            <i>Square reserved for the Logistics Department</i>
        </h4>
        <table>
            <tr>
                <td style="border: 1px solid #000000">
                    Condition of returned equipment
                </td>
                <td style="border: 1px solid #000000">
                    <input type="checkbox" style="vertical-align: top"> Good
                </td>
                <td style="border: 1px solid #000000">
                    <input type="checkbox" style="vertical-align: top"> Average
                </td>
                <td style="border: 1px solid #000000">
                    <input type="checkbox" style="vertical-align: top"> Bad
                </td>
            </tr>
            <tr>
                <td width="30%" height="60px" style="border: 1px solid #000000">
                    Observations
                </td>
                <td colspan="3" style="border: 1px solid #000000">
                    <b>{{ $asset->notes }}</b>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="border: 1px solid #000000">
                    User
                </td>
                <td style="border: 1px solid #000000">
                    <b>{{ $user ? $user->getFullNameAttribute() : '' }}</b>
                </td>
                <td style="border: 1px solid #000000">
                    Logistics Coordinator / Logistician
                </td>
                <td style="border: 1px solid #000000">
                    <b>{{ $asset->focal_point ? $asset->focal_point->getFullNameAttribute() : '' }}</b>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000000">
                    Name and signature
                </td>
                <td width="30%" height="60px" style="border: 1px solid #000000">
                    <b>{{ $user ? $user->getFullNameAttribute() : '' }}</b>
                </td>
                <td style="border: 1px solid #000000">
                    Name and signature
                </td>
                <td width="30%" style="border: 1px solid #000000">
                    <b>{{ $asset->focal_point ? $asset->focal_point->getFullNameAttribute() : '' }}</b>
                </td>
            </tr>
        </table>
    </body>

</html>
