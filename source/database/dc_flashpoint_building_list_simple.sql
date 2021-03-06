USE [EHSINFO]
GO
/****** Object:  StoredProcedure [dbo].[dc_flashpoint_building_list_simple]    Script Date: 2/24/2021 10:52:08 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- Create date: 2016-02-24
-- Description:	Get list of tickets, ordered and paged.
-- =============================================

ALTER PROCEDURE [dbo].[dc_flashpoint_building_list_simple]
	
	-- filter
	@filter_like		varchar(50)			= NULL		
	
AS	
	SET NOCOUNT ON;	
	
	-- Set up table var so we can reuse results.		
	DECLARE @tempMain TABLE
	(
		row_id				int,
		building_code		varchar(4), 
		building_name		varchar(255),
		address_street		varchar(max),
		address_city		varchar(50),
		address_zip			varchar(5),
		address_zip_sort	varchar(4)
	)
		
	-- Populate main table var. This is the primary query. Order
	-- and query details go here.

	INSERT INTO @tempMain (row_id, building_code, building_name, address_street, address_city, address_zip, address_zip_sort)	
			(SELECT ROW_NUMBER() OVER(ORDER BY _main.BuildingName ASC),
			RTRIM(LTRIM(_main.BuildingCode)), 
			RTRIM(LTRIM(_main.BuildingName)), 
			_main.StreetAddress1, 
			_main.city, 
			_main.Zip5, 
			_main.Zip4
	FROM UKSpace.dbo.MasterBuildings _main
	WHERE _main.BuildingName LIKE '%' + @filter_like + '%' OR _main.StreetAddress1 LIKE '%' + @filter_like + '%' OR _main.Zip5 LIKE '%' + @filter_like + '%' OR @filter_like IS NULL)	
	
	-- Extract paged rows from main tabel var.
	SELECT  *
	FROM @tempMain
	ORDER BY row_id
	
	
	
