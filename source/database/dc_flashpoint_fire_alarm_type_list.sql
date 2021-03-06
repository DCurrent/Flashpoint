USE [EHSINFO]
GO
/****** Object:  StoredProcedure [dbo].[fire_alarm_type_list]    Script Date: 2/23/2021 10:13:08 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- Create date: 2015-09-06
-- Description:	Return list of fire incident types for selection.
-- =============================================
CREATE PROCEDURE [dbo].[dc_flashpoint_fire_alarm_type_list]
	
	-- Parameters
AS	
BEGIN
	
	SET NOCOUNT ON;	 
	
		SELECT DISTINCT 
                      TOP (100) PERCENT 
                      id, 
                      label
		FROM         tbl_fire_alarm_type_list
		WHERE		active = 1
		ORDER BY label

					
END
