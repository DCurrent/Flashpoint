USE [EHSINFO]
GO
/****** Object:  StoredProcedure [dbo].[dc_flashpoint_area_list_simple]    Script Date: 2/24/2021 10:52:08 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- Create date: 2016-02-24
-- Description:	Get list of tickets, ordered and paged.
-- =============================================

CREATE PROCEDURE [dbo].[dc_flashpoint_area_list_simple]
	
	-- filter
	@filter_building_code		varchar(5)			= NULL		
	
AS	
	SET NOCOUNT ON;	
	
	-- Set up table var so we can reuse results.		
	DECLARE @tempMain TABLE
	(
		row_id				int,
		area_id				varchar(4), 
		barcode				varchar(6),
		usage_id			varchar(3),
		usage_desc			varchar(264),
		building_code		varchar(5),
		area_floor			varchar(3)
	)
		
	-- Populate main table var. This is the primary query. Order
	-- and query details go here.

	SELECT DISTINCT 
                         PERCENT RTRIM(LTRIM(UKSpace.dbo.Rooms.RoomID)) AS room, UKSpace.dbo.Rooms.LocationBarCodeID AS barcode, UKSpace.dbo.Rooms.RoomUsage AS useage, 
                         UKSpace.dbo.MasterRoomUsageCodes.UsageCodeDescr AS useage_desc, UKSpace.dbo.Rooms.Building AS facility, UKSpace.dbo.Rooms.Floor
FROM            UKSpace.dbo.Rooms LEFT OUTER JOIN
                         UKSpace.dbo.MasterRoomUsageCodes ON UKSpace.dbo.Rooms.RoomUsage = UKSpace.dbo.MasterRoomUsageCodes.UsageCode
ORDER BY useage, room

	INSERT INTO @tempMain (row_id, area_id, barcode, usage_id, usage_desc, building_code, area_floor)	
			(SELECT ROW_NUMBER() OVER(ORDER BY _main.BuildingName ASC),
			RTRIM(LTRIM(_main.RoomID)), 
			_main.LocationBarCodeID, 
			_main.StreetAddress1, 
			_main.RoomUsage, 
			_main.UsageCodeDescr, 
			_main.Building,
			_main.Floor
	FROM UKSpace.dbo.Rooms _main
	LEFT OUTER JOIN
                         UKSpace.dbo.MasterRoomUsageCodes AS _useage ON _main.RoomUsage = _usage.UsageCode
	WHERE _main.Building = @filter_building_code)	
	
	-- Extract paged rows from main tabel var.
	SELECT  *
	FROM @tempMain
	ORDER BY row_id
	
	
	
