using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace FamilyTreeSystem.Migrations
{
    /// <inheritdoc />
    public partial class AddFatherAndMotherIds : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<int>(
                name: "FatherId",
                table: "FamilyMembers",
                type: "INTEGER",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "MotherId",
                table: "FamilyMembers",
                type: "INTEGER",
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_FamilyMembers_FatherId",
                table: "FamilyMembers",
                column: "FatherId");

            migrationBuilder.CreateIndex(
                name: "IX_FamilyMembers_MotherId",
                table: "FamilyMembers",
                column: "MotherId");

            migrationBuilder.AddForeignKey(
                name: "FK_FamilyMembers_FamilyMembers_FatherId",
                table: "FamilyMembers",
                column: "FatherId",
                principalTable: "FamilyMembers",
                principalColumn: "Id",
                onDelete: ReferentialAction.SetNull);

            migrationBuilder.AddForeignKey(
                name: "FK_FamilyMembers_FamilyMembers_MotherId",
                table: "FamilyMembers",
                column: "MotherId",
                principalTable: "FamilyMembers",
                principalColumn: "Id",
                onDelete: ReferentialAction.SetNull);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_FamilyMembers_FamilyMembers_FatherId",
                table: "FamilyMembers");

            migrationBuilder.DropForeignKey(
                name: "FK_FamilyMembers_FamilyMembers_MotherId",
                table: "FamilyMembers");

            migrationBuilder.DropIndex(
                name: "IX_FamilyMembers_FatherId",
                table: "FamilyMembers");

            migrationBuilder.DropIndex(
                name: "IX_FamilyMembers_MotherId",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "FatherId",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "MotherId",
                table: "FamilyMembers");
        }
    }
}
